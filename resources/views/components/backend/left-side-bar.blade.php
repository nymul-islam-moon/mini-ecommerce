 <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
     <div class="sidebar-brand">
         <a href="./index.html" class="brand-link">
             <img src="{{ asset('backend/assets/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                 class="brand-image opacity-75 shadow" />
             <span class="brand-text fw-light">AdminLTE 4</span>
         </a>
     </div>
     <div class="sidebar-wrapper">
         <nav class="mt-2">
             <!--begin::Sidebar Menu-->
             <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                 aria-label="Main navigation" data-accordion="false" id="navigation">
 
                 <li class="nav-header">PRODUCTS</li>
                
                     <li class="nav-item">
                         <a href="" class="nav-link">
                             <p>Categories</p>
                         </a>
                     </li>
                     <li class="nav-item">
                         <a href="" class="nav-link">
                             <p>Sub-Categories</p>
                         </a>
                     </li>
                    
                
                    
                     <li class="nav-item">
                         <a href="" class="nav-link">
                             <p>{{ ucfirst('product') }}</p>
                         </a>
                     </li>
                
             </ul>

         </nav>
     </div>

 </aside>