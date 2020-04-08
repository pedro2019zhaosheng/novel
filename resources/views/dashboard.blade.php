
@include('header')
<!-- Sidebar -->
@include('sidebar')
  <!-- Content Header (Page header) -->
   <!-- Content Header (Page header) -->
   <!-- Content Wrapper. Contains page content -->
   <div class="content-wrapper">
    <section class="content-header">
        <h1>
            {!! $page_title or "Page Title" !!}
            <small>{{ $page_description or null }}</small>
        </h1>
        <!-- You can dynamically generate breadcrumbs here -->
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
        </ol> -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Your Page Content Here -->
        @yield('content')
    </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

@include('footer')