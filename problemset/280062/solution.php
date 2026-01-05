<?php

/**
 * @param integer $n
 * @param array $connections
 * @return string JSON
 */
function iranServerRoundTable(int $n, int $m, array $connections): mixed
{
    $adj = array_fill(1, $n, []);
    foreach ($connections as $edge) {
        $u = $edge[0];
        $v = $edge[1];

        $adj[$u][] = $v;
        $adj[$v][] = $u;
    }

    //* آرایه رنگ: -1 یعنی بدون رنگ، 0 و 1 دو رنگ مختلف
    $color = array_fill(1, $n, -1);

    $isBipartite = true;

    for ($start = 1; $start <= $n && $isBipartite; $start++) {
        if ($color[$start] !== -1) {
            continue;
        }

        // شروع BFS از این رأس
        $queue = [];
        $color[$start] = 0;
        $queue[] = $start;

        while (!empty($queue) && $isBipartite) {
            $u = array_shift($queue);

            foreach ($adj[$u] as $v) {
                if ($color[$v] === -1) {
                    // رنگ مخالف رأس فعلی
                    $color[$v] = 1 - $color[$u];
                    $queue[] = $v;
                } elseif ($color[$v] === $color[$u]) {
                    // دو رأس مجاور با رنگ یکسان → گراف دو بخشی نیست
                    $isBipartite = false;
                    break;
                }
            }
        }
    }

    if (!$isBipartite) {
        return json_encode([
            "possible" => "NO"
        ]);
    }

    // اگر دو بخشی بود، افراد را بر اساس رنگ در دو میز می‌ریزیم
    $table1 = [];
    $table2 = [];

    for ($i = 1; $i <= $n; $i++) {
        if ($color[$i] === 0) {
            $table1[] = $i;
        } else {
            $table2[] = $i;
        }
    }

    return json_encode([
        "possible" => "YES",
        "table_1"  => $table1,
        "table_2"  => $table2
    ]);
    // return $adj;
}

var_dump(iranServerRoundTable(6, 3,[
        [1, 2],
        [2, 3],
        [3, 4],
        [4, 5],
        [5, 6],
        [6, 1],
]));