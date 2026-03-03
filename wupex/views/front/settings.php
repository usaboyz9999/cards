<?php
Auth::requireLogin();
Helpers::redirect(Helpers::siteUrl('?page=account&tab=settings'));
