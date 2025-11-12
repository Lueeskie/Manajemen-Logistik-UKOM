<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /project_name/public/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?url=$1 [L,QSA]
</IfModule>