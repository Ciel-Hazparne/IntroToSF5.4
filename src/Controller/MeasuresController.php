<?php

namespace App\Controller;

use App\Entity\MeasureDateSearch;
use App\Form\MeasureDateSearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeasuresController extends AbstractController
{
    #[Route('/measures', name: 'measures')]
    public function index(ChartBuilderInterface $chartBuilder, HttpClientInterface $httpClient, Request $request): Response
    {

        $measures_temp = $httpClient->request('GET', 'http://127.0.0.1:8001/api/mesures?libelle=temperature_champ')->toArray();
        $measures_temp2 = $httpClient->request('GET', 'http://127.0.0.1:8001/api/mesures?libelle=temperature_serre')->toArray();
        $measures_humidity = $httpClient->request('GET', 'http://127.0.0.1:8001/api/mesures?libelle=humidite_champ')->toArray();
        $measures_humidity2 = $httpClient->request('GET', 'http://127.0.0.1:8001/api/mesures?libelle=humidite_serre')->toArray();

        $labels_humidity = [];
        $labels_humidity2 = [];
        $labels_temp = [];
        $labels_temp2 = [];

        $data_humidity = [];
        $data_humidity2 = [];
        $data_temp = [];
        $data_temp2 = [];

        if (isset($_REQUEST["submit"])) {
            $minDate = new \DateTime($_GET["minDate"]);
            $maxDate = new \DateTime($_GET["maxDate"]);
            $maxDate->modify('+ 1 day');

            $measures = [
                "measures_temp" => $measures_temp,
                "measures_temp2" => $measures_temp2,
                "measures_humidity" => $measures_humidity,
                "measures_humidity2" => $measures_humidity2
            ];

            foreach ($measures as $measureKey => $measureValue) {
                foreach ($measureValue as $measure) {
                    if (isset($measure["valeur"]) && isset($measure["createdAt"])) {
                        if (new \DateTime($measure["createdAt"]) > $minDate && new \DateTime($measure["createdAt"]) < $maxDate) {
                            switch ($measureKey) {
                                case "measures_temp":
                                    $data_temp[] = $measure["valeur"];
                                    $labels_temp[] = date("Y m-d H:i", strtotime($measure["createdAt"]));
                                    break;
                                case "measures_temp2":
                                    $data_temp2[] = $measure["valeur"];
                                    $labels_temp2[] = date("Y m-d H:i", strtotime($measure["createdAt"]));
                                    break;
                                case "measures_humidity":
                                    $data_humidity[] = $measure["valeur"];
                                    $labels_humidity[] = date("Y d/m - H:i", strtotime($measure["createdAt"]));
                                    break;
                                case "measures_humidity2":
                                    $data_humidity2[] = $measure["valeur"];
                                    $labels_humidity2[] = date("Y d/m - H:i", strtotime($measure["createdAt"]));
                                    break;
                            }
                        }
                    }
                }
            }
        } else {
            $measureKeys = [
                "measures_humidity" => ["data_humidity", "labels_humidity"],
                "measures_temp" => ["data_temp", "labels_temp"],
                "measures_humidity2" => ["data_humidity2", "labels_humidity2"],
                "measures_temp2" => ["data_temp2", "labels_temp2"]
            ];

            /* La boucle foreach parcourt le tableau $measureKeys et assigne chaque élément à la variable $measureKey.
            Les valeurs associées à chaque clé sont stockées dans le tableau ${$value[0]} (pour les données) et ${$value[1]} (pour les libellés).
            À l'intérieur de la boucle $measureKey, vous parcourez le tableau ${$measureKey} (supposons que cela représente les mesures pour une clé donnée).
            Pour chaque mesure, vous ajoutez la valeur ($measure["valeur"]) au tableau ${$value[0]} (par exemple, $data) et la date formatée
            (date("Y d/m - H:i", strtotime($measure["createdAt"]))) au tableau ${$value[1]} (par exemple, $labels).!*/

            foreach ($measureKeys as $measureKey => $value) {
                ${$value[0]} = [];
                ${$value[1]} = [];

                foreach (${$measureKey} as $measure) {
                    ${$value[0]}[] = $measure["valeur"];
                    ${$value[1]}[] = date("Y d/m - H:i", strtotime($measure["createdAt"]));
                }
            }
        }
        /*
            Les boucles foreach imbriquées ont été fusionnées en une seule boucle, en utilisant un tableau $measures qui contient toutes les mesures et leurs clés correspondantes.
            Une structure de commutation (switch) est utilisée pour stocker les données et les étiquettes dans les tableaux appropriés en fonction de la clé de la mesure.
            Dans la condition else, une autre boucle foreach est utilisée avec un tableau $measureKeys pour simplifier la récupération des données et des étiquettes correspondantes.
            Les variables sont créées dynamiquement à l'aide de ${$value[0]} et ${$value[1]} pour éviter la duplication du code.
         */

        // humidité champ
        $chart_humidity = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart_humidity->setData([
            'labels' => $labels_humidity, //axe des abscisses X (date et heure des données)
            'datasets' => [
                [
                    'label' => 'humidité champ',             //nom du graphique
                    'backgroundColor' => 'rgb(44, 62, 80)',  //couleur du fond du graphique
                    'borderColor' => 'rgb(24, 188, 156)',    //couleur des bords du graphique
                    'data' => $data_humidity,                //axe des ordonnées Y (measure)
                ],
            ],
        ]);

        // humidité serre
        $chart_humidity2 = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart_humidity2->setData([
            'labels' => $labels_humidity2,
            'datasets' => [
                [
                    'label' => 'humidité serre',
                    'backgroundColor' => 'rgb(44, 62, 80)',
                    'borderColor' => 'rgb(24, 188, 156)',
                    'data' => $data_humidity2,
                ],
            ],
        ]);

        //température champ
        $chart_temp = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart_temp->setData([
            'labels' => $labels_temp,
            'datasets' => [
                [
                    'label' => 'température champ',
                    'backgroundColor' => 'rgb(44, 62, 80)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $data_temp,
                ],
            ],
        ]);

        //température serre
        $chart_temp2 = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart_temp2->setData([
            'labels' => $labels_temp2,
            'datasets' => [
                [
                    'label' => 'température serre',
                    'backgroundColor' => 'rgb(44, 62, 80)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $data_temp2,
                ],
            ],
        ]);

        return $this->render('measures/index.html.twig', [
            'chart1' => $chart_humidity,
            'chart2' => $chart_temp,
            'chart3' => $chart_humidity2,
            'chart4' => $chart_temp2

        ]);
    }
}