<?php

function iranServerRoundTable(int $n, int $m, array $connections): string
{
    //* Validate input count
    if (count($connections) !== $m) {
        return json_encode(["possible" => "NO"]);
    }
    
    //* Build adjacency list and track edges
    $adj = array_fill(1, $n, []);
    $edgeSet = [];
    
    foreach ($connections as $edge) {
        if (!is_array($edge) || count($edge) !== 2) {
            return json_encode(["possible" => "NO"]);
        }
        $u = $edge[0];
        $v = $edge[1];
        
        //* Check for invalid node IDs
        if ($u < 1 || $u > $n || $v < 1 || $v > $n) {
            return json_encode(["possible" => "NO"]);
        }
        
        //* Check for self-loops
        if ($u === $v) {
            return json_encode(["possible" => "NO"]);
        }
        
        //* Handle duplicate edges
        $edgeKey = min($u, $v) . '-' . max($u, $v);
        if (!isset($edgeSet[$edgeKey])) {
            $edgeSet[$edgeKey] = true;
            $adj[$u][] = $v;
            $adj[$v][] = $u;
        }
    }

    //* BFS to check bipartiteness and identify components
    $color = array_fill(1, $n, -1);
    $isBipartite = true;
    $components = [];

    for ($start = 1; $start <= $n && $isBipartite; $start++) {
        if ($color[$start] !== -1) {
            continue;
        }

        //* nodes with color 0 and 1 in this component
        $component = [0 => [], 1 => []];
        $queue = [];
        $color[$start] = 0;
        $queue[] = $start;
        $component[0][] = $start;

        while (!empty($queue) && $isBipartite) {
            $u = array_shift($queue);
            foreach ($adj[$u] as $v) {
                if ($color[$v] === -1) {
                    $color[$v] = 1 - $color[$u];
                    $queue[] = $v;
                    $component[$color[$v]][] = $v;
                } elseif ($color[$v] === $color[$u]) {
                    $isBipartite = false;
                    break;
                }
            }
        }
        
        $components[] = $component;
    }

    if (!$isBipartite) {
        return json_encode(["possible" => "NO"]);
    }

    //* Now assign components to tables to minimize difference
    //* For each component, we can choose which partition goes to which table
    //* Use dynamic programming or greedy approach
    
    $table1 = [];
    $table2 = [];
    
    foreach ($components as $component) {
        $size0 = count($component[0]);
        $size1 = count($component[1]);
        
        $currentDiff = abs(count($table1) - count($table2));
        
        //* Try both assignments and choose the one that minimizes difference
        $diff1 = abs((count($table1) + $size0) - (count($table2) + $size1));
        $diff2 = abs((count($table1) + $size1) - (count($table2) + $size0));
        
        if ($diff1 <= $diff2) {
            $table1 = array_merge($table1, $component[0]);
            $table2 = array_merge($table2, $component[1]);
        } else {
            $table1 = array_merge($table1, $component[1]);
            $table2 = array_merge($table2, $component[0]);
        }
    }

    return json_encode([
        "possible" => "YES",
        "table_1"  => $table1,
        "table_2"  => $table2
    ]);
}