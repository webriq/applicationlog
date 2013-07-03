-- remove data

DELETE FROM "module"
      WHERE "module" = 'Grid\ApplicationLog';

DELETE FROM "user_right"
      WHERE "group"     = 'user.extra'
        AND "resource"  = 'applicationLog'
        AND "privilege" = 'view';
