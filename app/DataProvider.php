<?php
  class DataProvider {
    private string $json;
    private array $json_data;

    public function __construct(string $filePath) {
      try {
        $this->readJson($filePath);
      } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
      }
    }

    private function readJson(string $filePath): void {
      if (!file_exists($filePath))
        throw new Exception('File not found');

      $this->json = file_get_contents($filePath);
      $this->json_data = json_decode($this->json, true);
    }

    public function getJson(): string {
      return $this->json;
    }

    public function getJsonData(): array {
      return $this->json_data;
    }

    public function getEvents(): array {
      return $this->json_data['events'];
    }
  }
?>