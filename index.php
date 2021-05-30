<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="style.css">
	<link rel="icon" href="p5t.png">
	<title>Пять</title>
</head>
<body>
	<form action = "?php $_PHP_SELF ?" method = "POST" enctype="multipart/form-data">
		<p>
			<input type="text" id="text" name="text" class="input">
		</p>
		<p>
			<input type="submit" name="submit" value="Назвать город">
		</p>
	</form>
	<?php 
	$connect = mysqli_connect('localhost', 'mysql', 'mysql', 'towns');

	if (!connect) {
		die("Не подключились к базе данных");
	}

	$town = mysqli_query($connect, "SELECT * FROM `geo`");
	$town = mysqli_fetch_all($town);

	$tmpTown = mysqli_query($connect, "SELECT * FROM `tmptowns`");
	$tmpTown = mysqli_fetch_all($tmpTown);

	foreach ($tmpTown as $tmpCity) {
		echo $tmpCity[1] . "<br>";
	}

	if (isset($_POST['submit'])) {
		if (isset($_POST['text'])) {
			$userTown = $_POST['text'];

			$check = false;

			foreach ($town as $city) {
				if (mb_strtolower($userTown) == mb_strtolower($city[6])) {
					$check = true;
					break;
				} 
			}

			if ($check == true) {
				foreach ($tmpTown as $tmpCity) {
					if (mb_strtolower($userTown) == mb_strtolower($tmpCity[1])) {
						$check = false;
						break;
					} 
				}
			}

			if ($check == false) {
				echo "Такого города не существует или он уже был использован раньше";
				exit();
			}

			$lastSymbol;
			foreach ($tmpTown as $lastTown) {
				$lastSymbol = mb_substr($lastTown[6], -1);
			}

			if (mb_strtolower($lastSymbol) == mb_substr($userTown, 0, 1) || $lastSymbol == '') {
				mysqli_query($connect, "INSERT INTO `tmptowns` (`id`, `town`) VALUES (NULL, '$userTown')");
			} else {
				echo "Нужно ввести город на последнюю букву предыдущего города";
				exit();
			}

			$lastSymbol = mb_substr($userTown, -1);

			$chance = rand(970, 1000);

			if ($chance < 975) {

				foreach ($town as $city) {
					$check = true;
					foreach ($tmpTown as $tmpCity) {
						if (mb_strtolower($city[6]) == mb_strtolower($tmpCity[1])) {
							$check = false;
							break;
						}
					}
					if ((mb_strtolower($lastSymbol) == mb_substr($city[6], 0, 1) || mb_strtoupper($lastSymbol) == mb_substr($city[6], 0, 1)) && $check == true) {
						mysqli_query($connect, "INSERT INTO `tmptowns` (`id`, `town`) VALUES (NULL, '$city[6]')");
						break;
					}	
				}
			} else {
				echo "Вы победили";
				foreach ($tmpTown as $tmpCity) {
					mysqli_query($connect, "DELETE FROM `tmptowns` WHERE `tmptowns`.`id` = $tmpCity[0];");
				}
			}
			echo $chance;
		}
	}
	?>
</body>
</html>