<div class="store-header">
    <h2><a href="{!! url('store') !!}">{{ config('quazar.name', 'My Store') }}</a></h2>

    <div class="store-account" id="storeNavbar">
        <a class="btn btn-link" href="{{ url('store/products') }}">Products</a>
        <a class="btn btn-link" href="{{ url('store/plans') }}">Plans</a>
        <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            Account <span class="caret"></span>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
            @if (auth()->user())
                <li><a href="{{ url('user/settings') }}"><span class="fa fa-user"></span> Settings</a></li>
                <li><a href="{{ url('store/account/profile') }}"><span class="fa fa-id-card"></span> Profile</a></li>
                <li><a href="{{ url('store/account/purchases') }}"><span class="fa fa-dollar"></span> Purchases</a></li>
                <li><a href="{{ url('store/account/orders') }}"><span class="fa fa-truck"></span> Orders</a></li>
                <li><a href="{{ url('store/account/subscriptions') }}"><span class="fa fa-ticket"></span> Subscriptions</a></li>
                <li><a href="{{ url('logout') }}"><span class="fa fa-sign-out"></span> Logout</a></li>
            @else
                <li><a href="{{ url('login') }}"><span class="fa fa-sign-in"></span> Login</a></li>
            @endif
        </ul>
    </div>
    <div class="store-menu">
        <a href="{!! url('store/cart/contents') !!}">
            <span class="fa fa-shopping-cart"></span>
            <span class="cart-count"></span> Items
        </a>
    </div>
</div>