<?php

/**
 * Solves the problem of splitting people between two tables as a bipartite graph check.
 *
 * Given the number of people, the number of acquaintance relations, and the list of
 * connections, this function determines whether it is possible to seat people at
 * two tables so that no two acquainted people sit at the same table.
 *
 * - If such a seating is possible:
 *   Returns a JSON string with:
 *     - "possible": "YES"
 *     - "table_1": an array of person IDs seated at the first table
 *     - "table_2": an array of person IDs seated at the second table
 * - If it is not possible or the input is invalid:
 *   Returns a JSON string with:
 *     - "possible": "NO"
 *
 * @param int   $n           Number of people (labeled from 1 to n)
 * @param int   $m           Number of acquaintance relations (number of edges)
 * @param array $connections List of relations as pairs [A, B] representing an edge between A and B
 *
 * @return string JSON string containing whether it is possible and, if so, the seating for both tables
*/
function iranServerRoundTable(int $n, int $m, array $connections): string
{
    if (count($connections) !== $m) {
        return json_encode(["possible" => "NO"]);
    }

    $adj = array_fill(1, $n, []);
    foreach ($connections as $edge) {
        $u = $edge[0];
        $v = $edge[1];
        $adj[$u][] = $v;
        $adj[$v][] = $u;
    }

    $color = array_fill(1, $n, -1);
    $isBipartite = true;

    for ($start = 1; $start <= $n && $isBipartite; $start++) {
        if ($color[$start] !== -1) {
            continue;
        }

        $queue = [];
        $color[$start] = 0;
        $queue[] = $start;

        while (!empty($queue) && $isBipartite) {
            $u = array_shift($queue);
            foreach ($adj[$u] as $v) {
                if ($color[$v] === -1) {
                    $color[$v] = 1 - $color[$u];
                    $queue[] = $v;
                } elseif ($color[$v] === $color[$u]) {
                    $isBipartite = false;
                    break;
                }
            }
        }
    }

    if (!$isBipartite) {
        return json_encode(["possible" => "NO"]);
    }

    $table1 = [];
    $table2 = [];
    
    for ($i = 1; $i <= $n; $i++) {
        if ($color[$i] === 0) {
            $table1[] = $i;
        } else {
            $table2[] = $i;
        }
    }
    
    if (empty($table2)) {
        $moveToTable2 = $table1[array_rand($table1)];
        $table1 = array_diff($table1, [$moveToTable2]);
        $table2 = [$moveToTable2];
    }

    return json_encode([
        "possible" => "YES",
        "table_1"  => array_values($table1),
        "table_2"  => array_values($table2)
    ]);
}