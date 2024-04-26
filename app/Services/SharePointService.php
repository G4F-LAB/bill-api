<?php

namespace App\Services;
use GuzzleHttp\Client;

class SharePointService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function generateAccessToken($clientId, $clientSecret, $tenantId)
    {
        $url = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token";

        try {
            $response = $this->client->post($url, [
                'form_params' => [
                    'client_id' => $clientId,
                    'scope' => 'https://graph.microsoft.com/.default',
                    'client_secret' => $clientSecret,
                    'grant_type' => 'client_credentials',
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            return $body['access_token'];
        } catch (\Exception $e) {
            // Handle exception
            return $e;
        }
    }


    public function getSiteId($accessToken, $siteName)
    {
        $url = "https://graph.microsoft.com/v1.0/search/query";
    
        try {
            $response = $this->client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'requests' => [
                        [
                            'entityTypes' => ['site'],
                            'query' => [
                                'queryString' => $siteName,
                            ],
                        ],
                    ],
                    'search' => [
                        'query' => $siteName,
                        'search' => ['displayName']
                    ],
                    'region' => 'BRA' 
                ],
            ]);
    
            $body = json_decode($response->getBody(), true);
            
            // Verifica se há resultados
            if(isset($body['hitsContainers']) && !empty($body['hitsContainers'])) {
                $hits = $body['hitsContainers'][0]['hits'];
                // Percorre os hits para encontrar o hitId correspondente ao site
                foreach($hits as $hit) {
                    if(isset($hit['resource']['name']) && $hit['resource']['name'] === $siteName) {
                        return $hit['hitId'];
                    }
                }
            }
            
            return null; // Retorna null se o site não for encontrado
        } catch (\Exception $e) {
            // Handle exception
            return $e;
        }
    }
    


    public function getSiteAndDriveIds($accessToken, $siteUrl)
    {
        $url = "https://graph.microsoft.com/v1.0/sites/";
    
        try {
            $response = $this->client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                ]
            ]);
    
            return  json_decode($response->getBody(), true);
     
            $siteId = $body['id'];
    
            // Verifique se o site tem um drive associado
            if (isset($body['drive'])) {
                $driveId = $body['drive']['id'];
            } else {
                // Se não houver drive associado ao site, obtenha o drive usando outro endpoint
                $driveUrl = "https://graph.microsoft.com/v1.0/sites/{$siteId}/drives";
                $driveResponse = $this->client->request('GET', $driveUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Accept' => 'application/json',
                    ]
                ]);
             
                $driveBody = json_decode($driveResponse->getBody(), true);
                dd($driveBody);
                $driveId = $driveBody['value'][0]['id']; // Se houver várias unidades de armazenamento, ajuste isso conforme necessário
            }
    
            return [
                'siteId' => $siteId,
                'driveId' => $driveId
            ];
        } catch (\Exception $e) {
            // Handle exception
            return null;
        }
    }
    

    public function listFiles($accessToken, $siteId, $driveId, $folderPath)
{
    $url = "https://graph.microsoft.com/v1.0/sites/{$siteId}/drives/{$driveId}/items/{$folderPath}/children";

    try {
        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ]
        ]);

        $body = json_decode($response->getBody(), true);

        // Retornar apenas os nomes dos arquivos
        $fileNames = array_map(function($file) {
            return $file['name'];
        }, $body['value']);

        return $fileNames;
    } catch (\Exception $e) {
        // Handle exception
        return $e;
    }
}

}
