<?php
  class Calendar {
    const MAX_POSSIBLE_WEEKS_IN_MONTH = 6;
    const DAYS_IN_WEEK = 7;
    const CS_DAYS = ['Po', 'Út', 'St', 'Čt', 'Pá', 'So', 'Ne'];
    const CS_MONTHS = [
      'Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen',
      'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'
    ];

    private array $events;
    private int $month;
    private int $year;
    private int | null $activeDay;

    public function __construct(array $events = [], int $year = null, int $month = null, int $activeDay = null) {
      $this->events = $events;
      $this->month = $month ? $month : $this->getTodaysMonth();
      $this->year = $year ? $year : $this->getTodaysYear();
      $this->activeDay = $activeDay;
    }

    private function getTodayDate(): array {
      return getdate();
    }
    private function getTodaysMonth(): int {
      return $this->getTodayDate()['mon'];
    }
    private function getTodaysYear(): int {
      return $this->getTodayDate()['year'];
    }
    private function getTodaysDay(): int {
      return $this->getTodayDate()['mday'];
    }
    private function getActiveDate(): string {
      if (!$this->activeDay) return '';
      return $this->year.'-'.$this->month.'-'.$this->activeDay;
    }
    private function dayHasEvent(int $day): bool {
      foreach ($this->events as $event) {
        if ($event['date'] === $this->year.'-'.$this->month.'-'.$day) return true;
      }
      return false;
    }
    private function getEmptyCell(): string {
      return '<span class="w-11 inline-block"></span>';
    }
    private function getDayCell(int $day): string {
      $cssClasess = 'w-11 p-2 inline-block cursor-pointer border border-white rounded-full hover:bg-blue-200';
      if ($this->dayHasEvent($day)) $cssClasess .= ' font-extrabold';
      if ($this->activeDay === $day) $cssClasess .= ' bg-blue-500 text-white';
      else if ($this->getTodaysDay() === $day && $this->getTodaysMonth() === $this->month && $this->getTodaysYear() === $this->year) $cssClasess .= ' bg-blue-100 text-red-600';
      else $cssClasess .= ' bg-white';
      return '
        <span class="'.$cssClasess.'">
          <a href="/?year='.$this->getYear().'&month='.$this->getMonth().'&activeDay='.$day.'">'.$day.'</a>
        </span>';
    }
    private function getPrevMonth(): string {
      $month = $this->month;
      $year = $this->year;
      if ($this->month == 1) {
        $month = 12;
        $year--;
      } else {
        $month--;
      }
      return '/?year='.$year.'&month='.$month;
    }
    private function getNextMonth(): string {
      $month = $this->month;
      $year = $this->year;
      if ($this->month == 12) {
        $month = 1;
        $year++;
      } else {
        $month++;
      }
      return '/?year='.$year.'&month='.$month;
    }

    private function getCalendarHeader() {
      return '<div class="m-3 flex justify-between">
                <a href="'.$this->getPrevMonth().'"><</a>
                <h1 class="text-1xl font-bold text-center">'.$this->getMonthName().' '.$this->getYear().'</h1>
                <a href="'.$this->getNextMonth().'">></a>
              </div>';
    }
    private function getCalendarDaysHeader() {
      $weekDays = $this->getWeekDays();
      $calendarRow = '<div class="flex justify-between text-center">';
      for ($i = 1; $i <= 7; $i++) {
        $calendarRow .= '<span class="w-11 p-2 inline-block">'.$weekDays[$i - 1].'</span>';
      }
      $calendarRow .= '</div>';
      return $calendarRow;
    }

    private function getCalendarRows():string {
      $calendar = '';
      $firstDayOfMonthIndex = $this->getWeekDayIndexOfFirstDayOfMonth();

      $firstRow = $this->getCalendarRow($firstDayOfMonthIndex, 1);
      $calendar .= $firstRow->row;
      $currentDay = $firstRow->currentDay;

      for ($i = 1; $i < self::MAX_POSSIBLE_WEEKS_IN_MONTH; $i++) {
        $row = $this->getCalendarRow(0, $currentDay);
        $calendar .= $row->row;
        $currentDay = $row->currentDay;
      }

      return $calendar;
    }
    private function getCalendarRow(int $startFromWeekDayIndex, int $startDay): Object {
      $currentDay = $startDay;
      $calendarRow = '<div class="flex justify-between text-center">';
      for ($i = 1; $i <= 7; $i++) {
        if (($i) < $startFromWeekDayIndex) $calendarRow .= $this->getEmptyCell();
        else if ($currentDay > $this->getDaysInMonth()) $calendarRow .= $this->getEmptyCell();
        else {
          $calendarRow .= $this->getDayCell($currentDay);
          $currentDay++;
        }
      }
      $calendarRow .= '</div>';

      $returnObj = new stdClass();
      $returnObj->row = $calendarRow;
      $returnObj->currentDay = $currentDay;

      return $returnObj;
    }
    private function getEventList(): string {
      $events = $this->getEvents();
      if (!$events) return '';
      $eventList = '<div class="mt-5">';
      $eventList .= '<h2 class="text-1xl font-bold text-center">Události</h2>';
      $eventList .= '<ul class="">';
      foreach ($events as $event) {
        $eventList .= 
          '<li class="p-3">
            <h2 class="font-bold">'.$event['name'].'</h2>
            <p>'.$event['description'].'</p>
          </li>';
      }
      $eventList .= '</ul>';
      $eventList .= '</div>';
      return $eventList;
    }
    private function getEvents(): array {
      if (!$this->activeDay) return [];
      $events = [];
      foreach ($this->events as $event) {
        if ($event['date'] === $this->getActiveDate()) {
          array_push($events, $event);
        }
      }
      return $events;
    }

    public function getWeekDays(): array {
      return self::CS_DAYS;
    }
    public function getMonth(): int {
      return $this->month;
    }
    public function getYear(): int {
      return $this->year;
    }
    public function getActiveDay(): int | null{
      return $this->activeDay;
    }
    public function getMonthName(): string {
      return self::CS_MONTHS[$this->month - 1];
    }
    public function getDaysInMonth(): int {
      return date('t', mktime(0, 0, 0, $this->month, 1, $this->year));
    }
    public function getWeekDayIndexOfFirstDayOfMonth(): int {
      return date('N', mktime(0, 0, 0, $this->month, 1, $this->year));
    }

    public function getCalendar() {
      $calendar = '<div class="m-5 max-w-xs transition-all">';
      $calendar .= $this->getCalendarHeader();
      $calendar .= $this->getCalendarDaysHeader();
      $calendar .= $this->getCalendarRows();
      $calendar .= $this->getEventList();
      $calendar .= '</div>';
      return $calendar;
    }
  }
?>