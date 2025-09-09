<?php

// phpMyAdminログイン時にホスト名を入力してログインできるようにするための設定
unset($cfg['Servers']);

$cfg['AllowArbitraryServer'] = true;
