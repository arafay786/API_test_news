<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Goutte\Client;
use Illuminate\Http\Request;

class ApiScrapperController extends Controller
{
    private $results = array();

    public function scrapper()
    {

        
        // For Getting Date 
        $date = Carbon::yesterday(); 
        $currentDate =  $date->toDateTimeString();
        list($currentDate) = explode(' ',$currentDate);

        $client = new Client();
        $url = 'https://newsapi.org/v2/everything?q=top-headlines&from='.$currentDate.'&sortBy=publishedAt&apiKey=869c07239dd94d73ab9968aa8d31b3c4';

        $crawler = $client->request('GET', $url);
        
        $result = $client->getResponse()->getContent();
        $data = json_decode($result);
        $articles = $data->articles;
        $oneList = [];

        foreach ($articles as $article) {
            $carbonDate = Carbon::parse($article->publishedAt);
            $dateOnly = $carbonDate->toDateString();
            $oneList[] = (object)[   
                "author" => $article->author,
                "source" => $article->source->name,                
                "title" => $article->title,
                "description" => $article->description,
                "weburl" => $article->url,
                "publishedAt" => $dateOnly,
            ];
        }

        // return print_r($oneList);

        // -----------------------------------------------------------------------------------------

        $secClient = new Client();
        $secUrl = 'https://content.guardianapis.com/search?from-date=2023-10-25&q=cricket&api-key=ff9ad846-496f-4182-8a3a-3dc68137f394';
        $secCrawler = $secClient->request('GET', $secUrl);        
        $secApiResult = $secClient->getResponse()->getContent();
        $secData = json_decode($secApiResult);
        $secList = [];
        foreach ($secData->response->results as $article) {
            $secList[] = (object)[
                "author" => "Un Defined",
                "source" => "The Guardian",
                "title" => $article->webTitle,
                "description" => 'No Description',
                "weburl" => $article->webUrl,
                "publishedAt" => $article->webPublicationDate,  
            ];
        }

        // -----------------------------------------------------------------------------------------

        $thirdClient = new Client();
        $thirdurl = 'https://api.nytimes.com/svc/search/v2/articlesearch.json?q=cricket&pub_date='.$currentDate.'&api-key=6svSaEWJf5rLArh55Guttz4i5pwqTAfp';
        $thirdcrawler = $thirdClient->request('GET', $thirdurl);        
        $thirdResult = $thirdClient->getResponse()->getContent();
        $thirdApiData = json_decode($thirdResult);

        $ThirdList = [];
        foreach ($thirdApiData->response->docs as $article) {
            
            $linkLastPart = basename($article->web_url);

            $removeExtra = str_replace('.html', ' ', $linkLastPart);

            $removeExtradahes = str_replace('-', ' ', $removeExtra);

            // $TitleForNewYorkTime = ('<span style="text-transform: capitalize">'.$removeExtradahes.'</span>');
            
            $ThirdList[] = (object)[
                "author" => "Un Defined",
                "source" => $article->source,
                'title' => $removeExtradahes,
                "description" => $article->snippet.$article->lead_paragraph,
                "weburl" => $article->web_url,               
                "publishedAt" => $currentDate,
            ];
           
        }
        $masterdata = (array_merge($oneList, $secList ,$ThirdList));


        $wordCount = count($masterdata);


        if($wordCount != '0')
        {
             DB::DELETE("Delete from api_news");
        }

        foreach ($masterdata as $data) {
            DB::insert('INSERT INTO api_news (author, source ,title, description , weburl, publishedAt) VALUES (?, ?, ?, ?, ?, ?)', [
                $data->author,
                $data->source,
                $data->title,
                $data->description,
                $data->weburl,
                $data->publishedAt
            ]);
        }

        if($wordCount != '0')
        {
             echo 'your Data from API are submitted succefully';
        }
    }

    public function datafetch()
    {
        $API_return_data = DB::table('api_news')->get();
        return View('apidata',['API_return_data' => $API_return_data]);
    }

    public function search()
    {
        $search_text_title = $_GET['title'];
        $search_text_author = $_GET['author'];
        $search_text_source = $_GET['source'];
        $search_text_fromDate = $_GET['fromDate'];

        $API_Search_Data = DB::table('api_news')
        ->where('title', 'LIKE', '%'.$search_text_title.'%')

        ->where('author', 'LIKE', '%'.$search_text_author.'%')

        ->where('source', 'LIKE', '%'.$search_text_source.'%')

        ->where('publishedAt', 'LIKE', '%'.$search_text_fromDate.'%')

        ->get();

        $wordCount = count($API_Search_Data);

        if($wordCount = '0')
        {
            return redirect()->route('datafetch');
        }
        
        return View('search',['API_Search_Data' => $API_Search_Data]);
    }
}
