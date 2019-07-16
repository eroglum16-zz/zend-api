# Zend API

## Proje Tanımı

Amacımız Rest Api üzerinden hizmet veren bir yapı kurulması. Sunulan dökümanlar sanatçı ve albümlerinden oluşuyor.

## Projenin Ayağa Kaldırılması

Önce GitHub'dan repoyu çekin:

`$ git clone https://github.com/eroglum16/zend-api.git`

Projenin olduğu dizine giderek aşağıdaki komutu çalıştırın:

`$ docker-compose up`

Projenin gereksinimlerinin composer tarafından yüklenmesi biraz zaman alacaktır. 
Sonrasında bilgisayarınızdaki 0.0.0.0:8080 adresinden ve portundan uygulamaya erişebilirsiniz. 
Restful API modülünün login endpoint'inden giriş yapmadığınız sürece doköümanlara erişim izni verilmeyecektir.
Bunu verilen postman collection'ı kullanarak Postman üzerinden yapabilirsiniz. Login olduktan sonra gelen sunucu response header'larından bearer-token kopyalanmalı ve dökumanlara erişirken Authorization olarak bearer token seçilip kopyalanan token kullanılmalıdır.

## Endpointler

`0.0.0.0:8080/album-rest/login`

`0.0.0.0:8080/album-rest/document/{id}`

`0.0.0.0:8080/album-rest/document?{query_params: #keyword #id}`
