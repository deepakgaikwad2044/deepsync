@extends("layouts.layouts")

@section('content')

<style>
    body {
        background: #f4f6fb;
        font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
    }

    .wrapper {
        padding: 30px;
    }

    .header {
        text-align: center;
        margin-bottom: 30px;
    }

    .header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #222;
    }

    .header p {
        color: #666;
        margin-top: 6px;
    }

    /* GRID */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    /* CARD BOX */
    .box {
        background: #fff;
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        transition: 0.2s ease-in-out;
    }

    .box:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.10);
    }

    .box h2 {
        font-size: 18px;
        margin-bottom: 12px;
        color: #333;
    }

    /* spacing between components */
    .box > * {
        margin-bottom: 10px;
    }

    .full {
        margin-top: 25px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        margin: 20px 0 10px;
        color: #222;
    }

</style>

<div class="wrapper">

<div class="profile-header">
        <a href="{{ route('user.dashboard') }}" class="back-link">
          <i class="fas fa-arrow-left"></i>
        </a>


        <div style="width:42px"></div>
      </div>
      
    <div class="header">
        <h1>🧩 Pranchi Component Showcase</h1>
        <p>Beautiful UI demo of all components</p>
    </div>

    <!-- GRID -->
    <div class="grid">

        <div class="box">
            <h2>🟢 Buttons</h2>
            <x-button type="success">Save Data</x-button>
            <x-button type="danger">Remove Data</x-button>
            <x-button type="success" text="Done" />
            <x-button text="Save" />
        </div>

        <div class="box">
            <h2>🔴 Alerts</h2>
            <x-alert type="danger" message="Something went wrong" />
            <x-alert message="Saved!" />
            <x-alert type="success">Saved via slot</x-alert>
        </div>

        <div class="box">
            <h2>🟠 Card</h2>
            <x-card>
                <x-alert type="success">Welcome!</x-alert>
                <x-button type="primary">Continue</x-button>
            </x-card>
        </div>

        <div class="box">
            <h2>🧬 Nesting</h2>
            <x-alert type="success">
                Data saved successfully
                <div style="margin-top:10px;">
                    <x-button type="primary">OK</x-button>
                </div>
            </x-alert>
        </div>

        <div class="box">
            <h2>🧭 Navbar</h2>
         <x-navbar brand="MyApp">
    <a href="#">Dashboard</a>
    <a href="#">Profile</a>
</x-navbar>
        </div>

        <div class="box">
            <h2>📊 Dashboard</h2>
            <x-dashboard />
        </div>

    </div>

    <!-- FULL WIDTH -->
    <div class="full">

        <div class="box">
            <div class="section-title">⚫ Card with Props</div>

            <x-card title="Notification" footer="Pranchi blade">

                <x-alert type="success">
                    Data saved successfully.
                </x-alert>

                <x-button type="primary">
                    Continue
                </x-button>

            </x-card>
        </div>

    </div>
    
    
</div>

@endsection