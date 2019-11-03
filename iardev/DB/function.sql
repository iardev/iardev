DROP FUNCTION IF EXISTS summery_audit(text,hstore,text,hstore);
create function summery_audit(text,hstore,text,hstore) returns text AS
$$
DECLARE
  ret text;
begin
ret := 'Out: '||to_char(date($2 -> 'checkoutdate'),'MM/DD/YYYY') || '. Due: ' || to_char(date($2 -> 'duedate'),'MM/DD/YYYY');
IF $1='I' then
   IF $3='latefees' then                                                                                                        
       return ret ||'. In: '||(date($2 ->'checkindate') )||'. Fees:'|| ($2 -> 'latefees');
   ELSE
       return ret ;
   END if;
END if;
IF $1='U' then
   return ret || '.  New Due: '||to_char(date($4->'duedate'),'MM/DD/YYYY');
END IF;

IF $1='D' then
   IF $3='latefees' then
       return ret ||'. In: '||(date($2 ->'checkindate') )||'. Fees:'|| ($2 -> 'latefees');
   ELSE
       return ret ;
   END if;
END IF;

return 'Unrecognized history: ';
end;

$$
language plpgsql;


