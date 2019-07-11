# Zend API

## Proje Tanımı

Amacımız Rest Api üzerinden hizmet veren bir yapı kurulması. Sunulan dökümanlar sanatçı ve albümlerinden oluşuyor.

## Projenin Ayağa Kaldırılması

Önce GitHub'dan repoyu çekin:

`$ git clone https://github.com/eroglum16/zend-api.git`

Projenin olduğu dizine giderek aşağıdaki komutları çalıştırın:

`$ docker build -t zend-api .`

## Endpointler

`(url)/document/{id}`

`(url)/document?{query_params: #keyword #id}`
