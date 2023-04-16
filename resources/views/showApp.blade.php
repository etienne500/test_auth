@extends('layouts.base')

@section('content')
    <style>.container {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        margin: 0 auto;
        max-width: 800px;
      }
      
      .container h1 {
        font-size: 36px;
        margin-bottom: 20px;
      }
      
      .container p {
        font-size: 18px;
        margin-bottom: 10px;
      }
      
      .container .btn {
        margin-top: 20px;
        font-size: 18px;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        color: white;
        background-color: #007bff;
        border: none;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: all 0.2s ease;
      }
      
      .container .btn:hover {
        background-color: #0069d9;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      }
      
      .container .btn-danger {
        background-color: #dc3545;
      }
      
      .container .btn-danger:hover {
        background-color: #c82333;
      }

      .container p {
  font-size: 1.2rem;
  line-height: 1.5;
  color: #333;
  margin-bottom: 20px;
}

      
    </style>
    <div class="container">
        <h1>{{ $application->name }}</h1>

        <p><?php echo "<b>id :  </b>"?> {{ $application->id }}</p>
        <p><?php echo "<b>return_url :  </b>"?>{{ $application->return_url }}</p>
        <p><?php echo "<b>public_key :  </b>"?>{{ $application->public_key }}</p>
        <p><?php echo "<b>secret_key :  </b>"?>{{ $application->secret_key }}</p>
        <p><?php echo "<b>(id:public_key) : </b>". ($application->id.':'.$application->public_key); ?></p>

        <a href="{{ route('applications.edit', ['id' => $application->id]) }}" class="btn btn-warning">Modifier</a>
    </div>
@endsection
