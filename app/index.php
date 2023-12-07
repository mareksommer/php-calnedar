<!DOCTYPE html>
<html lang="cs">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP Calendar</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
  </head>

  <body>
    <?php
      require_once 'DataProvider.php';
      require_once 'Calendar.php';

      $dataProvider = new DataProvider('data.json');
      $data = $dataProvider->getEvents();

      $queries = array();
      parse_str($_SERVER['QUERY_STRING'], $queries);
      $year = $queries['year'] ?? null;
      $month = $queries['month'] ?? null;
      $activeDay = $queries['activeDay'] ?? null;

      $calendar = new Calendar($data, $year, $month, $activeDay);
      echo $calendar->getCalendar();
    ?>
  </body>
</html>



