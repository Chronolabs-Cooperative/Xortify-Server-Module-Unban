# Xortify Server 5
## Module: Unban
## Author: Simon Antony Roberts <wishcraft@users.sourceforge.net>

This module is for the xortify.com server unbanning module, this is for operations with the open honeypot see: http://sourceforge.net/projects/xortify

# Rewrite: SEO Friendly URLS [.htaccess]

This goes in the XOOPS_ROOT_PATH/.htaccess file listed in the order of occurence required.

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^unban/latest/([0-9]+)/ipsec.html$ ./modules/unban/index.php?op=latest&num=$1&extra=toliet [L,NC,QSA]
    RewriteRule ^unban/remove/ipsec.html$ ./modules/unban/index.php?op=remove&extra=shower [L,NC,QSA]
    RewriteRule ^unban/retracted/([0-9]+)/(.*?)/(.*?)/ipsec.html$ ./modules/unban/index.php?op=member&member_id=$1&ip=$3 [L,NC,QSA]
    RewriteRule ^unban/index.php(.*?)$ ./modules/unban/index.php$1 [L,NC,QSA]
    RewriteRule ^unban/backend.php(.*?)$ ./modules/unban/backend.php$1 [L,NC,QSA]
    RewriteRule ^unban/$ ./modules/unban/index.php [L,NC,QSA]
