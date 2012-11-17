<?php
$memcache = new Memcache();

$memcache->addServer('127.0.0.1'); // edit here if your memcached server differs from localhost

$list = array();
$allSlabs = $memcache->getExtendedStats('slabs');
$items = $memcache->getExtendedStats('items');
foreach($allSlabs as $server => $slabs) {
    foreach($slabs AS $slabId => $slabMeta) {
        $cdump = $memcache->getExtendedStats('cachedump',(int)$slabId);
        foreach($cdump AS $server => $entries) {
            if($entries) {
                foreach($entries AS $eName => $eData) {
                    $list[$eName] = array(
                        'key' => $eName,
                        'value' => $memcache->get($eName)
                    );
                }
            }
        }
    }
}
ksort($list);

if (isset($_GET['del'])) {
    $memcache->delete($_GET['del']);

    header("Location: " . $_SERVER['PHP_SELF']);
}

if (isset($_GET['flush'])) {
    $memcache->flush();

    header("Location: " . $_SERVER['PHP_SELF']);
}

if (isset($_GET['set'])) {
    $memcache->set($_GET['set'], $_GET['value']);

    header("Location: " . $_SERVER['PHP_SELF']);
}
?>
<head>
    <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css" rel="stylesheet">
    <script type="text/javascript" src="http://cachedcommons.org/cache/jquery/1.4.2/javascripts/jquery.js"></script>
    <script type="text/javascript" src="http://cachedcommons.org/cache/jquery-table-sorter/2.0.3/javascripts/jquery-table-sorter-min.js"></script>

</head>
<body>

<div class="container" style="width: 940px;">
    <h3>memcached</h3>
    <table cellpadding="0" cellspacing="0" class="tablesorter table table-bordered table-hover table-striped">
        <thead>
        <tr>
            <th>key</th>
            <th>value</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($list as $i): ?>
            <tr>
                <td><?php echo $i['key'] ?></td>
                <td><?php echo $i['value'] ?></td>
                <td><a href="memcached.php?del=<?php echo $i['key'] ?>">X</a>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <center>
        <a href="memcached.php?flush=1">FLUSH</a> <br />
        <br />
        <a href="#" onclick="memcachedSet()">SET</a>
    </center>

    <script type="text/javascript">
        $(document).ready(function(){
            $("table").tablesorter();
        });

        function memcachedSet() {
            key = prompt("Key: ");
            value = prompt("Value: ");

            window.location.href = "memcached.php?set="+ key +"&value=" + value;
        }
    </script>
</body>