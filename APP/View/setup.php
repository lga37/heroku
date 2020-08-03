<br><br>
<div class="row">
    <div class="col-md-12">
        

        <h1>Setup</h1>

CREATE DATABASE `webjj`;USE `webjj`; 
CREATE USER 'webjj'@'localhost' IDENTIFIED BY 'admin123';
GRANT CREATE,SELECT,INSERT,UPDATE,DELETE ON webjj.* TO 'webjj'@'localhost';
FLUSH PRIVILEGES;
DROP TABLE IF EXISTS `categs`;DROP TABLE IF EXISTS `prods`;DROP TABLE IF EXISTS `prods_categs`;






        <a href="<?=URL.'?c=setup&a=installSchema'?>" class="btn btn-lg btn-block btn-outline-success">INSTALL</a>
        <br>



        <br>

        <h3>Fazendo a Migracao Arquivo CSV</h3>


        <table class="table table-sm table-hover table-striped">
        <form method="post" enctype="multipart/form-data" action="<?=URL.'?c=setup&a=seed'?>">
        
            <div class="md-form my-0">
                <input type="file" name="arquivo" class="form-control mr-sm-2">
            </div>

            <button class="btn btn-block btn-info">UPLOAD</button>
        </form>
        </table>



        <h1>
            <a href="<?=URL.'?c=setup&a=reset'?>">Zerar registros BD</a>
        </h1>


        <?php
        if(isset($m0)) echo $m0;
        if(isset($m1)) echo $m1;
        if(isset($m2)) echo $m2;
        if(isset($m3)) echo $m3;
        ?>


    </div>
</div>
