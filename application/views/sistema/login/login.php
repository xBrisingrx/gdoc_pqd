<!-- Login -->
<section class="container g-pt-100 g-pb-20">
  <div class="row justify-content-between">
    <div class="col-md-6 col-lg-5 flex-md-unordered g-mb-80">
      <div class="g-brd-around g-brd-gray-light-v3 g-bg-white rounded g-px-30 g-py-50 mb-4">
        <header class="text-center mb-4">
          <h1 class="h3 g-color-black g-font-weight-300 text-capitalize">Identificación de usuario</h1>
        </header>

        <!-- Form -->
        <?php echo form_open('Login/login', array('id' => 'form_login', 'class' => 'g-py-5', 'method' => 'POST') )?>
        
          <!-- Token generado automaticamente en el controlador -->
          <input id="token" name="token" type="hidden" value="<?php echo $token;?>">
          <div class="mb-4">
            <div class="input-group g-brd-primary--focus">
              <span class="input-group-addon g-width-45 g-brd-gray-light-v3 g-color-gray-dark-v5">
                  <i class="icon-finance-067 u-line-icon-pro"></i>
                </span>
              <input id="username" name="username" class="form-control g-color-black g-brd-gray-light-v3 g-py-15 g-px-15" type="text" placeholder="Usuario" required>
            </div>
          </div>

          <div class="mb-4">
            <div class="input-group g-brd-primary--focus mb-4">
              <span class="input-group-addon g-width-45 g-brd-gray-light-v3 g-color-gray-dark-v5">
                  <i class="icon-media-094 u-line-icon-pro"></i>
                </span>
              <input id="password" name="password" class="form-control g-color-black g-brd-gray-light-v3 g-py-15 g-px-15" type="password" placeholder="Contraseña" required>
            </div>
          </div>
          <div class="mb-4">
            <button class="btn btn-block u-btn-primary g-py-13" type="submit">Login</button>
          </div>
        </form>
        <!-- End Form -->

            <?php if (!empty($this->session->flashdata('error-login'))): ?>
                  <div class="alert alert-danger" role="alert">
                      <strong><i class="fa fa-exclamation-circle"></i></strong> <?php echo $this->session->flashdata('error-login'); ?>
                  </div>
            <?php endif ?>

      </div>
    </div>

    <div class="col-md-6 flex-md-first g-mb-80">
      <div class="mb-5">
        <h2 class="h1 g-font-weight-300 mb-3">Bienvenido!</h2>
        <p class="g-color-gray-dark-v5">Ingrese usuario y contraseña, luego precione Login para ingresar al sistema.</p>
      </div>

      <div class="row">
        <div class="col-lg-9">
          <!-- Icon Blocks -->
          <div class="media mb-4">
            <div class="d-flex mr-3">
              <span class="align-self-center u-icon-v1 u-icon-size--lg g-color-primary">
                  <i class="fa fa-folder-open-o"></i>
                </span>
            </div>
            <div class="media-body align-self-center">
              <h3 class="h5">Sistema de control documentario</h3>
            </div>
          </div>
          <!-- End Icon Blocks -->
        </div>
      </div>
    </div>
  </div>
</section>
<!-- End Login -->

