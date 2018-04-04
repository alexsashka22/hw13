<?php

header ("Content-Type: text/html; charset=utf-8");

$action = !empty($_GET['action']) ? $_GET['action'] : null;
$orderBy = "date_add";
$sortVariants = ['date_add', 'description', 'is_done'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=hw13', 'root', '');

    if (isset($_POST['sort']) && !empty($_POST['sort_by']) && in_array($_POST['sort_by'], $sortVariants)) {
        $orderBy = $_POST['sort_by'];
    }

    $sql = "SELECT * FROM tasks ORDER BY $orderBy";
    $sth = $pdo->prepare($sql);
    $sth->execute();

    if (!empty($_POST['description'])) {
        $pdo->exec("INSERT INTO tasks (id, description, is_done, date_added) VALUES (NULL, '".$_POST['description']."', 0, NOW());");

        if ($sql) {
            echo "<p>Данные успешно добавлены в таблицу.</p>";
        } else {
            echo "<p>Произошла ошибка.</p>";
        }
    }

    if (isset($action) && !empty($_GET['id'])) {
        $id = (int)$_GET['id'];

        if ($action == 'edit') {
            $sth = $pdo->prepare("SELECT description FROM tasks WHERE id = ?");
            $sth->execute([$id]);
        }

        if ($action == 'done') {
    	      $sth=$pdo->prepare("UPDATE tasks SET is_done = 1 WHERE id = ?");
            $sth->execute([$id]);

            if ($sql) {
                echo "<p>Задание выполнено.</p>";
            } else {
                echo "<p>Произошла ошибка.</p>";
            }
        }

        if ($action == 'delete') {
    	    $sth=$pdo->prepare("DELETE FROM tasks WHERE id = ?");
            $sth->execute([$id]);

            if ($sql) {
                echo "<p>Задание удалено.</p>";
            } else {
                echo "<p>Произошла ошибка.</p>";
            }
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
            <option value="date_add">Дате добавления</option>
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
				  <td><?php echo ($row['is_done'] ? "<span style='color: green;'>Выполнено</span>" : "<span style='color: orange;'>В процессе</span>"); ?></td>
				  <td>
				      <a href="?id=<?= $row['id']?>&action=edit">Изменить</a>
				      <a href="?id=<?= $row['id']?>&action=done">Выполнить</a>
				      <a href="?id=<?= $row['id']?>&action=delete">Удалить</a>
				  </td>
			</tr>
				<?php } ?>
    </tbody>
</table>
</body>
</html>
