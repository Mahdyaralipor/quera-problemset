<?php

use PHPUnit\Framework\TestCase;

class roundTableSampleTest extends TestCase
{
    protected $answer_file_path = __DIR__.'/../solution.php';

    protected function initialize()
    {
        $this->assertFileExists($this->answer_file_path);
        require_once $this->answer_file_path;
        $this->assertTrue(
            function_exists('iranServerRoundTable'),
            'تابع iranServerRoundTable تعریف نشده است.'
        );
    }
    
    public function test_impossible_case()
    {
        $this->initialize();

        $n = 5;
        $m = 5;
        $connections = [
            [1, 2], [2, 3], [3, 4], [4, 5], [5, 1]
        ];
        $result = json_decode(iranServerRoundTable($n, $m, $connections), true);
        
        $this->assertEquals("NO", $result["possible"]);
    }
    
    public function test_single_person()
    {
        $this->initialize();
        
        $n = 1;
        $m = 0;
        $connections = [];
        $expectedSizes = [0, 1];
        $expectedTables = [[1], []];
        $result = json_decode(iranServerRoundTable($n, $m, $connections), true);
        
        $this->assertEquals("YES", $result["possible"]);
        
        $tables = [$result["table_1"], $result["table_2"]];
        sort($tables);
        sort($expectedTables);        
        $this->assertEquals($expectedTables, $tables);

        $actualSizes = [count($result["table_1"]), count($result["table_2"])];
        sort($actualSizes);
        $this->assertEquals($expectedSizes, $actualSizes);
    }

    
    public function test_two_groups()
    {
        $this->initialize();

        $n = 4;
        $m = 2;
        $connections = [
            [1, 2], [3, 4]
        ];
        $expectedSizes = [2, 2];
        $expectedTables = [[[1, 3], [2, 4]], [[1, 4], [2, 3]]];
        $result = json_decode(iranServerRoundTable($n, $m, $connections), true);
        
        $this->assertEquals("YES", $result["possible"]);
        
        $tables = [$result["table_1"], $result["table_2"]];
        
        foreach ($tables as &$table) {
            sort($table);
        }
        sort($tables);
        $foundMatch = false;
        foreach ($expectedTables as $expected) {
            foreach ($expected as &$group) {
                sort($group);
            }
            sort($expected);

            if ($tables === $expected) {
                $foundMatch = true;
                break;
            }
        }

        $this->assertTrue($foundMatch);

        $actualSizes = [count($result["table_1"]), count($result["table_2"])];
        sort($actualSizes);
        $this->assertEquals($expectedSizes, $actualSizes);
    }

    
    public function test_people_with_no_connections()
    {
        $this->initialize();

        $n = 3;
        $m = 0;
        $connections = [];
        $expectedSizes = [1, 2];
        $expectedTables = [[[1], [2, 3]], [[3], [1, 2]], [[2], [1, 3]]];
        $result = json_decode(iranServerRoundTable($n, $m, $connections), true);
        
        $this->assertEquals("YES", $result["possible"]);
        
        $tables = [$result["table_1"], $result["table_2"]];

        foreach ($tables as &$table) {
            sort($table);
        }
        sort($tables);
        $foundMatch = false;
        foreach ($expectedTables as $expected) {
            foreach ($expected as &$group) {
                sort($group);
            }
            sort($expected);

            if ($tables === $expected) {
                $foundMatch = true;
            }
        }
        $this->assertTrue($foundMatch);

        $actualSizes = [count($result["table_1"]), count($result["table_2"])];
        sort($actualSizes);
        $this->assertEquals($expectedSizes, $actualSizes);
    }
}
