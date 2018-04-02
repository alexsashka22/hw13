<?php

header ("Content-Type: text/html; charset=utf-8");

$description = "";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=hw13', 'root', '');

    if (isset($_POST['description'])) {
        $description = $_POST['description'];

        $sql = ("INSERT INTO tasks (description, is_done) VALUES ('$description', '0')");
    } else {
		    $sql = ("select * from tasks");
		}

    if (isset($action) && !empty($_GET['id'])) {
        $id = (int)$_GET['id'];

        if ($action == 'edit') {
            $sth = $pdo->prepare("SELECT description FROM tasks WHERE id = ?");
            $sth->execute([$id]);
        }

        if ($action =='done') {
    	      $sth=$pdo->prepare("UPDATE tasks SET is_done = 1 WHERE id=?");
            $sth->execute([$id]);
        }

        if ($action=='delete') {
    	    $sth=$pdo->prepare("DELETE FROM tasks WHERE id = ?");
            $sth->execute([$id]);
        }

        $sql = ("select * from tasks");
    }
} catch (PDOException $e) {
    die("Error!: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html>
    <head>
		  	<meta charset="utf-8">
		  	<title>Задания</title>
			  <style>
            table {
                border-spacing: 0;
                border-collapse: collapse;
            }

            table td, table th {
                border: 1px solid #ccc;
                padding: 5px;
            }

            table th {
                background: #eee;
            }
        </style>

    </head>
		<body>
			  <h1>Список дел на сегодня</h1>

 <div style="float: left">
    <form method="POST">
        <input type="text" name="description" placeholder="Описание задачи" value="">
        <input type="submit" name="save" value="Добавить">
    </form>
</div>
<div style="float: left; margin-left: 20px;">
    <form method="POST">
        <label for="sort">Сортировать по:</label>
        <select name="sort_by">
            <option value="date_created">Дате добавления</option>
            <option value="is_done">Статусу</option>
            <option value="description">Описанию</option>
        </select>
        <input type="submit" name="sort" value="Отсортировать">
    </form>
</div>
<div style="clear: both"></div>

<table>
    <tbody>
			<tr>
        <th>Описание задачи</th>
        <th>Дата добавления</th>
        <th>Статус</th>
        <th></th>
      </tr>
      <?php foreach ($pdo->query($sql) as $row) { ?>
			<tr>
				  <td><?php echo $row['description']; ?></td>
				  <td><?php echo $row['date_added']; ?></td>
				  <td><span style="color: rgb(235, 180, 15);"><?php echo $row['is_done']; ?></span></td>
				  <td>
				      <a href="index.php?id=<?= $row['id']?> &action=edit">Изменить</a>
				      <a href="index.php?id=<?= $row['id']?> &action=done">Выполнить</a>
				      <a href="index.php?id=<?= $row['id']?> &action=delete">Удалить</a>
				  </td>
			</tr>
				<?php } ?>
    </tbody>
</table>
</body>
</html>
