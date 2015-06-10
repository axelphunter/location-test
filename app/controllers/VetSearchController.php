<?php

use Toin0u\Geotools\Facade\Geotools;
use Entities\Vet;

class VetSearchController extends \BaseController
{

    public function getAll()
    {
        $vet = Vet::all();

        return \Response::json(array(
            'error' => false,
            'result' => $vet->toArray()),
            200
        );

    }

    public function postLocation()
    {
        $location = \Input::get('location');

        $data_arr = Geocoder::geocode($location);

        $coordA   = Geotools::coordinate([$data_arr['latitude'], $data_arr['longitude']]);
        $vets = Vet::all();
        foreach($vets as $vet)
        {
            $distance_set = $vet->distance;

            if($vet->latitude != null && $vet->longitude != null)
            {
                $coordB   = Geotools::coordinate([$vet->latitude, $vet->longitude]);
                $distance = Geotools::distance()->setFrom($coordA)->setTo($coordB);
                if($distance->in('km')->haversine() < $distance_set) {
                    $vet['distance'] = $distance->in('km')->haversine();
                    $result[] = $vet;
                }
                else {
                    continue;
                }
            }
            else {
                continue;
            }
        }

        if(empty($result)){
            return \Response::json(['error' => true, 'message' => 'There are no vets in this area']);
        }

        return \Response::json(array(
            'error' => false,
            'result' => $result),
            200
        );

    }


}
