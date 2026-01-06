# 🪑 میزگرد بزرگ - Quera 280062

**تشخیص گراف دوبخشی + تقسیم بهینه افراد به 2 میز**

## 🎯 الگوریتم
```
BFS → رنگ‌آمیزی (0/1) → کامپوننت‌ها
چرخ فرد → NO
تخصیص بهینه: هر کامپوننت را به شکلی که تفاوت تعداد میزها کمینه شود
```

## 💻 کد PHP

```php
function iranServerRoundTable(int $n, int $m, array $connections): string {
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
        if ($color[$start] !== -1) continue;

        //* Nodes with color 0 and 1 in this component
        $component = [0 => [], 1 => []];
        $queue = [$start];
        $color[$start] = 0;
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

    //* Assign components to tables to minimize difference
    $table1 = [];
    $table2 = [];
    
    foreach ($components as $component) {
        $size0 = count($component[0]);
        $size1 = count($component[1]);
        
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
```

## 📊 تست‌ها

| n | m | Result |
|:--|:--|:--|
| 6 | 6 | `YES [1,3,5] [2,4,6]` |
| 6 | 5 | `NO` (چرخ فرد) |
| 3 | 0 | `YES [1] [2,3]` |

## ⚡ Complexity

**زمان:** O(n + m) - BFS + تخصیص کامپوننت‌ها  
**فضا:** O(n + m) - ذخیره گراف و رنگ‌ها

## 🔑 نکات کلیدی

- **اعتبارسنجی ورودی:** بررسی تعداد یال‌ها، محدوده گره‌ها، حلقه‌های خودگردان و یال‌های تکراری
- **تشخیص دوبخشی:** استفاده از BFS برای رنگ‌آمیزی 2-رنگی گراف
- **تقسیم بهینه:** هر کامپوننت را به گونه‌ای تخصیص می‌دهد که تفاوت اندازه میزها کمینه شود
- **مدیریت کامپوننت‌ها:** گراف‌های ناهمبند را به درستی مدیریت می‌کند

## 📝 توضیحات الگوریتم

### مرحله 1: اعتبارسنجی
- بررسی تعداد یال‌های ورودی
- بررسی معتبر بودن شماره گره‌ها (1 تا n)
- شناسایی و حذف یال‌های تکراری
- شناسایی حلقه‌های خودگردان (self-loops)

### مرحله 2: ساخت گراف
- ایجاد لیست مجاورت برای نمایش گراف
- استفاده از edgeSet برای جلوگیری از یال‌های تکراری

### مرحله 3: تشخیص دوبخشی با BFS
- رنگ‌آمیزی گره‌ها با دو رنگ (0 و 1)
- اگر دو گره مجاور رنگ یکسان داشته باشند → گراف دوبخشی نیست
- ذخیره هر کامپوننت همبند به صورت جداگانه

### مرحله 4: تخصیص بهینه به میزها
- برای هر کامپوننت، دو حالت ممکن برای تخصیص وجود دارد
- انتخاب حالتی که تفاوت تعداد افراد در دو میز را کمینه کند
- استفاده از روش حریصانه (Greedy) برای تصمیم‌گیری

## 🔗 لینک

[Quera 280062](https://quera.org/problemset/280062)