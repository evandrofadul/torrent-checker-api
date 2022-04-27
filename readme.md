## Torrent Checker & API
Aplicação web em PHP que possibilita verificar informações de arquivo torrent utilizando o *Magnet Link* do mesmo.

## Informações exibidas
- nome
- *hash*
- tamanho
- arquivos contidos
- data de criação
- *trackers*
- *seeders*
- *leechers*
- quantidade de downloads completos.

Inicialmente as informações são exibidas em html utilizando o método `$_POST`, porém também é possível obter as informações via `$_GET` no qual é gerada uma nova página em formato Json com os dados.

## Funcionamento
Pela interface web é necessário inserir o *Magnet Link* no campo do formulário, *magnet* então é lido e em seguida separado o *hash* dos *trackers*. As primeiras informações são obitidas em um servidor remoto de *caching torrent files* (alguns torrents não são indexados, pois não estão no *cache* deste servidor). Em seguida os trackers são separados em um *array* e os metadados são coletados utilizando *scrapping*.

## API
As informações são obitidas utilizando request ```/?torrent=[MagnetLink]``` e o resultado é codificado em Json.

## DEMO
https://tor-chec-api.herokuapp.com/

