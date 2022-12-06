    <!-- Header -->
    <header id="js-header" class="u-header" data-header-fix-moment="500" data-header-fix-effect="slide">
      <!-- Top Bar -->
      <div class="u-header__section g-brd-bottom g-brd-gray-light-v4 g-bg-black g-transition-0_3">
        <div class="container">
          <div class="row justify-content-between align-items-center g-mx-0--lg">
            <div class="col-sm-auto g-pos-rel g-py-8">
              <?php if (!empty($this->session->userdata('nombre'))): ?>
                <div class="text-white">
                  Usuario activo: <?php echo $this->session->userdata('nombre') ?>
                </div>
              <?php endif ?>
            </div>

            <div class="col-sm-auto g-pr-15 g-pr-0--sm">
              <a href="<?php echo base_url('Login/logout'); ?>" class="btn btn-sm btn-info my-1">Cerrar sesion</a>
            </div>
          </div>
        </div>
      </div>
      <!-- End Top Bar -->

      <div class="u-header__section u-header__section--light g-bg-white g-transition-0_3 g-py-10" data-header-fix-moment-exclude="g-bg-white g-py-10" data-header-fix-moment-classes="g-bg-white-opacity-0_7 u-shadow-v18 g-py-0">
        <nav class="js-mega-menu navbar navbar-expand-lg">
          <div class="container">
            <!-- Responsive Toggle Button -->
            <button class="navbar-toggler navbar-toggler-right btn g-line-height-1 g-brd-none g-pa-0 g-pos-abs g-top-3 g-right-0" type="button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navBar" data-toggle="collapse" data-target="#navBar">
              <span class="hamburger hamburger--slider">
            <span class="hamburger-box">
              <span class="hamburger-inner"></span>
              </span>
              </span>
            </button>
            <!-- End Responsive Toggle Button -->
            <!-- Logo -->
            <a href="<?php echo base_url('');?>" class="navbar-brand">
              <img src="<?php echo base_url('assets/img/logo2.png');?>" alt="Image Description">
            </a>
            <!-- End Logo -->

            <!-- Navigation -->
            <div class="collapse navbar-collapse align-items-center flex-sm-row g-pt-10" id="navBar">
              <ul class="navbar-nav ml-auto text-uppercase g-font-weight-600 u-main-nav-v5 u-sub-menu-v1">
                <li class="nav-item hs-has-sub-menu g-mx-20--lg g-mb-5 g-mb-0--lg">
                  <a href="<?php echo base_url('Personas');?>" class="nav-link" id="nav-personas" aria-haspopup="true" aria-expanded="false" aria-controls="nav-submenu-1">Personal
                  </a>
                  <!-- Submenu personas -->
                  <ul class="hs-sub-menu list-unstyled g-mt-20--lg g-mt-10--lg--scrolling" id="nav-submenu-personas" aria-labelledby="nav-personas">
                    <li>
                      <a href="<?php echo base_url('Personas');?>">Ver personas</a>
                    </li>
                    <?php if ($this->session->userdata('rol') == 1): ?>
                      <li>
                        <a href="<?php echo base_url('Personas/new');?>">Nueva persona</a>
                      </li>
                    <?php endif ?>
                    
                    <li>
                      <a href="<?php echo base_url('Asignacion_Perfiles/administrar/1');?>">Asignacion de perfiles</a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('Perfiles/index/1');?>">Admin de perfiles</a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('Atributos/index/1');?>">Admin de atributos</a>
                    </li>
<!--                     <li>
                      <a href="<?php echo base_url('Atributos/dependencias_atributos/1');?>">Dependencia de atributos</a>
                    </li> -->
                    <li>
                      <!-- <a href="<?php echo base_url('Empresas/administrar/1');?>">Admin de empresas</a> -->
                    </li>
                    <li>
                      <a href="<?php echo base_url('Motivos_baja/administrar/1');?>">Admin motivos baja</a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('Entrega_ropa');?>">Entrega de ropa</a>
                    </li>
                    <!-- <li>
                      <a href="<?php echo base_url('Personas/vacaciones');?>">Vacaciones</a>
                    </li> -->
                  </ul>
                  <!-- End Submenu personas -->
                </li>
                <li class="nav-item hs-has-sub-menu g-mx-20--lg g-mb-5 g-mb-0--lg">
                  <a href="<?php echo base_url('Vehiculos');?>" class="nav-link" id="nav-link-1" aria-haspopup="true" aria-expanded="false" aria-controls="nav-submenu-1">Vehiculos</a>
                  <!-- Submenu Vehiculos -->
                  <ul class="hs-sub-menu list-unstyled g-mt-20--lg g-mt-10--lg--scrolling" id="nav-submenu-1" aria-labelledby="nav-link-1">
                    <li>
                      <a href="<?php echo base_url('Vehiculos');?>">Ver vehiculos</a>
                    </li>
                    <?php if ($this->session->userdata('rol') == 1): ?>
                      <li>
                        <a href="<?php echo base_url('Vehiculos/new');?>">Nuevo vehiculo</a>
                      </li>
                    <?php endif ?>
                    <li>
                      <a href="<?php echo base_url('Asignacion_Perfiles/administrar/2');?>">Asignacion de perfiles</a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('Perfiles/index/2');?>">Admin de perfiles</a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('Atributos/index/2');?>">Admin de atributos</a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('Asignaciones_vehiculo');?>">Admin asignaciones</a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('Aseguradoras');?>">Admin aseguradoras</a>
                    </li>
<!--                     <li>
                      <a href="<?php echo base_url('Atributos/dependencias_atributos/2');?>">Dependencia de atributos</a>
                    </li> -->
                    <li>
                      <!-- <a href="<?php echo base_url('Empresas/administrar/2');?>">Admin de empresas</a> -->
                    </li>
                    <li>
                      <a href="<?php echo base_url('Motivos_baja/administrar/2');?>">Admin motivos baja</a>
                    </li>
                  </ul>
                  <!-- End Submenu Vehiculos -->
                </li>
<!--                 <li class="nav-item g-mx-20--lg g-mb-5 g-mb-0--lg">
                  <a href="<?php echo base_url('Contratos');?>" class="nav-link">Contratos</a>
                </li> -->
                <li class="nav-item hs-has-sub-menu g-mx-20--lg g-mb-5 g-mb-0--lg">
                  <a href="<?php echo base_url('Documentos');?>" class="nav-link" id="nav-documentos" aria-haspopup="true" aria-expanded="false" aria-controls="nav-submenu-1">Documentos
                  </a>
                  <!-- Submenu documentos -->
                  <ul class="hs-sub-menu list-unstyled g-mt-20--lg g-mt-10--lg--scrolling" id="nav-submenu-documentos" aria-labelledby="nav-documentos">
                    <li>
                      <a href="<?php echo base_url('Documentos');?>">Registros de personal</a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('Documentos/registro_vehiculos');?>">Registros de vehiculos</a>
                    </li>
                  </ul>
                  <!-- End Submenu documentos -->
                </li>
                <li class="nav-item hs-has-sub-menu g-mx-20--lg g-mb-5 g-mb-0--lg">
                  <a href="<?php echo base_url('Informes/personal') ?>" class="nav-link" id="nav-informes" aria-haspopup="true" aria-expanded="false" aria-controls="nav-submenu-1">Informes
                  </a>
                  <!-- Submenu informes -->
                  <ul class="hs-sub-menu list-unstyled g-mt-20--lg g-mt-10--lg--scrolling" id="nav-submenu-informes" aria-labelledby="nav-informes">
                    <li>
                      <a href="<?php echo base_url('Informes/personal');?>">Informes de personal</a>
                    </li>
                    <li>
                      <a href="<?php echo base_url('Informes/vehiculos');?>">Informes de vehiculos</a>
                    </li>
                  </ul>
                  <!-- End Submenu informes -->
                </li>
                <?php if ($this->session->userdata('rol') == 1): ?>
                  <li class="nav-item hs-has-sub-menu g-mx-20--lg g-mb-5 g-mb-0--lg">
                    <a href="<?php echo base_url('Usuarios');?>" class="nav-link" id="nav-seguridad" aria-haspopup="true" aria-expanded="false" aria-controls="nav-submenu-1">Seguridad
                    </a>
                    <!-- Submenu seguridad -->
                    <ul class="hs-sub-menu list-unstyled g-mt-20--lg g-mt-10--lg--scrolling" id="nav-submenu-seguridad" aria-labelledby="nav-seguridad">
                      <li>
                        <a href="<?php echo base_url('Usuarios');?>">Admin. usuarios</a>
                      </li>
                    </ul>
                    <!-- End Submenu seguridad -->
                  </li>
                <?php endif ?>
              </ul>
            </div>
            <!-- End Navigation -->
          </div>
        </nav>
      </div>
    </header>
    <!-- End Header -->
