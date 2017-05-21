@layout('layout.head', ['title' => 'Welcome'])
    
    <main>
        
        <h1>Welcome to Teapot</h1>
        
        <ul>
            <li>url: {{$data->url()}}</li>
            <li>{{$assets}}</li>
        </ul>
        
    </main>
    
@layout('layout.foot')