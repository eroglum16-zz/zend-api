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

        $request_auth = $this->params()->fromHeader('Authorization')->getFieldValue();

        $auth_string = $this->user . ":" . $this->pass;

        if ($request_auth=="Basic ".base64_encode($auth_string)) {

            $token = sha1(rand());
            header("Bearer-Token:".$token);

            $redis = new Predis\Client(array(
                "scheme" => "tcp",
                "host" => "127.0.0.1",
                "port" => 6379));

            $exp_time = strtotime("+15 minutes");

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
            "scheme" => "tcp",
            "host" => "127.0.0.1",
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
        $client = Elasticsearch\ClientBuilder::create()->build();
        $defaultHandler = Elasticsearch\ClientBuilder::defaultHandler();
        $client = Elasticsearch\ClientBuilder::create()
            ->setHandler($defaultHandler)
            ->build();
        $connectionPool = '\Elasticsearch\ConnectionPool\StaticNoPingConnectionPool';
        $client = Elasticsearch\ClientBuilder::create()
            ->setConnectionPool($connectionPool)
            ->build();
        $selector = '\Elasticsearch\ConnectionPool\Selectors\StickyRoundRobinSelector';
        $client = Elasticsearch\ClientBuilder::create()
            ->setSelector($selector)
            ->build();
        $serializer = '\Elasticsearch\Serializers\SmartSerializer';
        $client = Elasticsearch\ClientBuilder::create()
            ->setSerializer($serializer)
            ->build();
        /* -------------------------------------------------- */

        $albums = $this->albumTable->fetchAll();

        $array = (array) $albums->current();

        $multi_array = array();
        array_push($multi_array,$array);

        foreach ($albums as $album){
            array_push($multi_array, $album);
        }

        $params = [
            'index' => 'my_index',
            'body'  => ['id'=>1,'hi'=>"to"]
        ];

        $client->index($params);

        $params = [
            'index' => 'my_index',
            'body'  => [
                'query' => [
                    'match' => [
                        'artist' => 'The Military Wives',
                    ],
                    'match' => [
                        'title' => 'The Military Wives',
                    ]
                ]
            ]
        ];

        $results = $client->search($params);


        //echo $results['hits']['totals'];
        var_dump($multi_array);



        //var_dump($multi_array);

        //return new JsonModel($results["hits"]["hits"][0]["_source"]);
        exit();


        /* -------------  Showing the Documents ------------- */
        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id != 0) {
            $results = $this->albumTable->getAlbumArray($id);
            $response = $this->getResponse();
            $response->setStatusCode(200);

            return new JsonModel($results);
        }else {

            if($this->params()->fromQuery('id')){
                $id = (int) $this->params()->fromQuery('id');

                if ($id==null) return new JsonModel(array());

                $results = $this->albumTable->getAlbumArray($id);
                $response = $this->getResponse();
                $response->setStatusCode(200);

                return new JsonModel($results);
            }elseif ($this->params()->fromQuery('keyword')){
                return new JsonModel(array());
            }else{
                return new JsonModel(array());
            }
        }
        /* -------------------------------------------------- */


    }
}
