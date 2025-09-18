 <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
     <div class="sidebar-brand">
         <a href="{{ route('backend.dashboard') }}" class="brand-link">
             <img src="{{ asset('backend/assets/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                 class="brand-image opacity-75 shadow" />
             <span class="brand-text fw-light">{{ config('app.name') }}</span>
         </a>
     </div>
     <div class="sidebar-wrapper">
         <nav class="mt-2">
             <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" aria-label="Main navigation" data-accordion="false" id="navigation">
 
                <li class="nav-header">PRODUCTS</li>
                <x-backend.left-side-bar-item name="Categories" route="" />
                <x-backend.left-side-bar-item name="Sub-Categories" route="" />
                <x-backend.left-side-bar-item name="products" route="" />
                
             </ul>

         </nav>
     </div>

 </aside>