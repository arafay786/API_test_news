@extends('layouts.app-master')

@section('content')
<!DOCTYPE html> 
<html> 
<head> 
    <title>GeeksforGeeks</title> 
</head> 
<body> 
   
    <marquee>Need to login to see the result</marquee>

    @auth
    <div id="colorlib-main">
      <section class="ftco-section ftco-no-pt ftco-no-pb">
        <div class="container">
          <form type="get" action="{{ url('/search') }}">
            <div class="row">
              <div class="col">
                <input type="text" class="form-control" name="author" placeholder="Author">
              </div>
              <div class="col">
                <input type="text" class="form-control" name="title" placeholder="Title">
              </div>

              <div class="col">
                <input type="text" class="form-control" name="source" placeholder="Souce">
              </div>

              <div class="col">
                <input type="text" class="form-control" name="fromDate" placeholder="From Date">
              </div>

              <div class="col">
                <button type="submit" class="btn btn-primary">Search</button>
              </div>
            </div>
          </form>


          <div class="row d-flex">
            <div class="col-xl-8 py-5 px-md-5">
              @foreach($API_return_data as $article)
              <div class="row pt-md-4 text-center">  
                <div class="col-md-12">
                  <div class="blog-entry ftco-animate d-md-flex fadeInUp ftco-animated">
                    <a href="single.html" class="img img-2" style="background-image: url('images/image_1.jpg');"></a>
                    <div class="text text-2 pl-md-4">
                      <h3 class="mb-2"><a href="{{ $article->weburl }}" style="text-transform: capitalize">{{ $article->title }}</a></h3>
                      <div class="meta-wrap">
                        <p class="meta">
                          <span><i class="icon-calendar mr-2"></i>{{$article -> publishedAt }}</span>
                          <span><i class="icon-folder-o mr-2"></i>{{$article -> source }}</span>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              @endforeach  
            </div>
          </div>
        </div>
      </section>
    </div>
    @endauth
  </body> 
</html> 

@endsection