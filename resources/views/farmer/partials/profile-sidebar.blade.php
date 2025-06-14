<div class="col-md-3 sidebar-nav">
    <a href="{{ route('farmer.profile.show') }}" class="{{ request()->routeIs('farmer.profile.show') ? 'active' : '' }}">
        Account
    </a>
    <a href="{{ route('farmer.profile.payout') }}" class="{{ request()->routeIs('farmer.profile.payout') ? 'active' : '' }}">
        Payout
    </a>
    <a href="{{ route('farmer.profile.address') }}" class="{{ request()->routeIs('farmer.profile.address') ? 'active' : '' }}">
        Address
    </a>
</div>
