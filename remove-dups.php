<html>
<head>
<title>Online PHP Script Execution</title>
</head>
<body>
<?php
$arr = array(
    0 => 2011-06-21,
    1 => 2011-06-22,
    2 => 2011-06-22,
    3 => 2011-06-23,
    4 => 2011-06-23,
    5 => 2011-06-24,
    6 => 2011-06-24,
    7 => 2011-06-25,
    8 => 2011-06-25,
    9 => 2011-06-26,
    10 => 2011-06-26,
    11 => 2011-06-26,

);

function get_keys_for_duplicate_values($my_arr, $clean = false)
{
    if ($clean) {
        return array_unique($my_arr);
    }
    $dups = $new_arr = array();
    foreach ($my_arr as $key => $val) {
        if (!isset($new_arr[$val])) {
            $new_arr[$val] = $key;
        } else {
            if (isset($dups[$val])) {
                $dups[$val][] = $key;
            } else {
                $dups[$val] = array($key);
                // Comment out the previous line, and uncomment the following line to
                // include the initial key in the dups array.
                // $dups[$val] = array($new_arr[$val], $key);
            }
        }

    }

    return $dups;
}

$dups = get_keys_for_duplicate_values($arr);

function flatten($items)
{
    $result = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            $result[] = $item;
        } else {
            array_push($result, ...array_values($item));
        }
    }

    return $result;
}

$dups = flatten($dups);

foreach ($dups as $d) {
    error_log($d);
}

?>
</body>
</html>