# 🪑 میزگرد بزرگ - Quera 280062

**تشخیص گراف دوبخشی + تقسیم افراد به 2 میز**

## 🎯 الگوریتم
```

BFS → Color(0/1) → table_1/table_2
چرخ فردد → NO
m=0 → 1 نفر تصادفی جابجا

```

## 💻 کد PHP

```php
function iranServerRoundTable(int $n, int $m, array $connections): string {
    if (count($connections) !== $m) return json_encode(["possible" => "NO"]);

    $adj = array_fill(1, $n, []);
    foreach ($connections as $edge) {
        [$u, $v] = $edge;
        $adj[$u][] = $v; $adj[$v][] = $u;
    }

    $color = array_fill(1, $n, -1);
    $isBipartite = true;

    for ($i = 1; $i <= $n && $isBipartite; $i++) {
        if ($color[$i] !== -1) continue;
        
        $q = [$i]; $color[$i] = 0;
        while ($q && $isBipartite) {
            $u = array_shift($q);
            foreach ($adj[$u] as $v) {
                if ($color[$v] === -1) {
                    $color[$v] = 1 - $color[$u];
                    $q[] = $v;
                } elseif ($color[$v] === $color[$u]) {
                    $isBipartite = false; break 2;
                }
            }
        }
    }

    if (!$isBipartite) return json_encode(["possible" => "NO"]);

    $t1 = $t2 = [];
    for ($i = 1; $i <= $n; $i++) {
        $color[$i] === 0 ? $t1[] = $i : $t2[] = $i;
    }
    
    if (empty($t2)) {
        $move = $t1[array_rand($t1)];
        $t1 = array_diff($t1, [$move]);
        $t2 = [$move];
    }

    return json_encode([
        "possible" => "YES",
        "table_1" => array_values($t1),
        "table_2" => array_values($t2)
    ]);
}
```


## 📊 تست‌ها

| n | m | Result |
| :-- | :-- | :-- |
| 6 | 6 | `YES [1,3,5] [2,4,6]` |
| 6 | 5 | `NO` |
| 3 | 0 | `YES [^1] [2,3]` |

## ⚡ Complexity

**O(n + m)**

🔗 [Quera 280062](https://quera.org/problemset/280062)
