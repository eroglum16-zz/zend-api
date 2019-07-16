<?php
namespace AlbumRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

use Album\Model\AlbumTable;
use Zend\View\Model\JsonModel;

use Predis;
use Elasticsearch;

class AlbumRestController extends AbstractRestfulController
{

    /** @var  AlbumTable $albumTable */
    protected $albumTable;
    protected $userTable;

    /* Zingat User */
    protected $user = "zingat";
    protected $pass = "office";

    public function __construct(AlbumTable $albumTable)
    {
        $this->albumTable = $albumTable;

    }

    public function loginAction()
    {

        if ($this->params()->fromHeader('Authorization')==null){
            http_response_code(401);
            echo "You need to send username and password as headers!";
            exit();
        }
        $request_auth = $this->params()->fromHeader('Authorization')->getFieldValue();

        $auth_string = $this->user . ":" . $this->pass;

        if ($request_auth=="Basic ".base64_encode($auth_string)) {

            $token = sha1(rand());
            header("Bearer-Token:".$token);

            $redis = new Predis\Client(array(
                "host" => "php7_cache",
                "port" => 6379));

            $exp_time = strtotime("+5 minutes");

            $redis->lpush($token, $token);
            $redis->lpush($token, $exp_time);

        }else{
            echo "Access denied!";
        }



        exit();
    }

    public function documentAction()
    {

        //Authorization string from headers
        $authorization = $this->params()->fromHeader('Authorization')->getFieldValue();

        if (substr($authorization,0,6)=="Bearer"){
            $bearer_token =  substr($authorization,7);
        }

        /* ---------- Redis Configurations ---------- */
        $redis = new Predis\Client(
            array(
                "host" => "php7_cache",
                "port" => 6379
            )
        );
        /* ------------------------------------------ */


        /* ---------- Validating the Token via Redis ---------- */
        $token_values = $redis->lrange($bearer_token,0,1);
        if(!isset($token_values[1])){
            http_response_code(401);
            echo "Your token is invalid!";
            exit();
        }elseif ($token_values[0]<strtotime("now")) {
            http_response_code(401);
            echo "Your token has expried!";
            exit();
        }
        /* -------------------------------------------------- */

        /* ---------- Elastic Search Configurations ---------- */

        $hosts = ['elasticsearch:9200'];
        $client = Elasticsearch\ClientBuilder::create()->setHosts($hosts)->build();

        $albums = $this->albumTable->fetchAllArray();

        foreach ($albums as $album){
            $params = [
                'index' => 'albums',
                'body'  => $album,
                'type' => 'album',
                'id' => $album['id']
            ];

            $client->index($params);
        }

        /* -------------------------------------------------- */



        /* -------------  Showing the Documents ------------- */
        $id = (int) $this->params()->fromRoute('id', 0);

        //  (url)/document/{id}
        if ($id != 0) {
            $results = $this->albumTable->getAlbumArray($id);
            $response = $this->getResponse();
            $response->setStatusCode(200);

            return new JsonModel($results);
        }else {
            //  (url)/document?#id
            if($this->params()->fromQuery('id')){
                $id = (int) $this->params()->fromQuery('id');

                if ($id==null) return new JsonModel(array());

                $results = $this->albumTable->getAlbumArray($id);
                $response = $this->getResponse();
                $response->setStatusCode(200);

                return new JsonModel($results);
            }
            //  (url)/document?#keyword
            elseif ($this->params()->fromQuery('keyword')){



                $keyword = $this->params()->fromQuery('keyword');



                $params = [
                    'index' => 'albums',
                    'body'  => [
                        'query' => [
                            'match' => ['artist'=>$keyword]
                        ]
                    ]
                ];

                $results = $client->search($params);



                if ($results['hits']['total']['value']==0){

                    $params = [
                        'index' => 'albums',
                        'body'  => [
                            'query' => [
                                'match' => ['title'=>$keyword]
                            ]
                        ]
                    ];
                    $results = $client->search($params);
                }

                return new JsonModel($results["hits"]["hits"][0]["_source"]);

            }else{
                return new JsonModel(array());
            }
        }
        /* -------------------------------------------------- */


    }

}
