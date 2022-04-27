<?php require_once('./engine/torrent.php'); require_once('./engine/scraper.php');
    if (isset($_GET['torrent'])) {
        echo header('Content-type: json');
        preg_match('/h:(.*?)&/', ($_GET['torrent']), $hashInfo);
        preg_match_all('/&tr=(.*?)&/', urldecode($_GET['torrent']), $magnetInfo);

        $torrent = new Torrent("http://itorrents.org/torrent/{$hashInfo[1]}.torrent");

        if ($torrent->size() != null) {
            $torrentFile ='';
            foreach ($torrent->content() as $key => $item) {
                $file .= str_replace("{$torrent->name()}/", '', $key) . '*';
            }
            $torrentFiles = array_filter(explode('*', $file));
            $torrentData = [
                'hash' => $hashInfo[1],
                'nome' => $torrent->name(),
                'criado' => date('d-m-Y H:i:s', $torrent->creation_date()),
                'tamanho' => $torrent->size(2),
                'arquivos' => $torrentFiles,
                'trackers' => $magnetInfo[1]
            ];

            $scraper = new Scrapeer\Scraper();
            $tracker = $magnetInfo[1];
            $hash = $hashInfo[1];
            $info = $scraper->scrape($hash, $tracker);
            $torrentScrape = [
                'seeds' => $info[$hash]['seeders'],
                'peers' => $info[$hash]['leechers'],
                'completed' => $info[$hash]['completed']
            ];
            $torrentApi = array_merge($torrentData, $torrentScrape);

            echo json_encode($torrentApi, JSON_UNESCAPED_UNICODE);
            
        } else {
            echo json_encode(['error' => 'torrent not found']);
        }
        return http_response_code(200);
    } else 
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Torrent API</title>
    <style>
        html,
        h2 {
            margin: 0;
        }
        body {
            color: rgba(0,0,0,0.87);
            font-family: Roboto, Helvetica, Arial, sans-serif;
            width: 60%;
            display: block;
            background-color: #f5f5f5;
            margin: 20px auto auto 20px;
        }
        .form input {
            width: 100%;
            padding: 8px;
            margin-top: 8px;
            margin-bottom: 8px;
        }
        .form button {
            padding: 8px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <form method="post" class="form">
            <span><h2>Magnet Link:</h2></span>
            <input type="url" name="torrent" value="<?php if (isset($_POST['torrent'])){echo $_POST['torrent'];} ?>">
            <button type="submit">Torrent Checker & API</button>
    </form>
    <?php
    if (isset($_POST['torrent'])){
        $json = file_get_contents('http://localhost/?torrent=' . urlencode($_POST['torrent']));
        $data = json_decode($json, true);

        if (isset($data['error'])) {
            echo $data['error'];
        } else {
            $torrentHtml = '<form method="get"><button type="submit" name="torrent" value="'. $_POST['torrent'] .'">GET API (Json)</button></form>';

            $torrentHtml .= "<br>Nome: <b>{$data['nome']}</b>";
            $torrentHtml .= "<br>Informações do Hash: <b>{$data['hash']}</b>";
            $torrentHtml .= "<br>Tamanho: <b>{$data['tamanho']}</b>";
            $torrentHtml .= "<br>Criado em: <b>{$data['criado']}</b>";
            $torrentHtml .= "<br>Downloads Completos: <b>{$data['completed']}</b>";
            $torrentHtml .= "<br>Seeds: <b>{$data['seeds']}</b>";
            $torrentHtml .= "<br>Peers: <b>{$data['peers']}</b>
                            <br>Arquivos:";
            foreach ($data['arquivos'] as $item) {
                $torrentHtml .= "<br/>&emsp;<b>$item</b>";
            }
            $torrentHtml .= '<br>Trackers:';
            foreach ($data['trackers'] as $item) {
                $torrentHtml .= "<br>&emsp;<b>$item</b>";
            }
            echo $torrentHtml;
        }
    }
    ?>
</body>
</html>