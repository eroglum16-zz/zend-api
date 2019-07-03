<?php
namespace AlbumRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

use Album\Model\Album;
use Album\Form\AlbumForm;
use Album\Model\AlbumTable;
use Zend\View\Model\JsonModel;

class AlbumRestController extends AbstractRestfulController
{

    /** @var  AlbumTable $albumTable */
    protected $albumTable;

    public function __construct(AlbumTable $albumTable)
    {
        $this->albumTable = $albumTable;
    }

    public function documentAction()
    {

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
            }


        }

    }

    public function get($id)
    {
        # code...
    }

    public function create($data)
    {
        # code...
    }

    public function update($id, $data)
    {
        # code...
    }

    public function delete($id)
    {
        # code...
    }
}
