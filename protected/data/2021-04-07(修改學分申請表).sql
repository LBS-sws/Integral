alter table gr_credit_request modify column audit_date datetime;
alter table gr_credit_request modify column apply_date datetime;
alter table gr_credit_request add confirm_date datetime NULL DEFAULT NULL COMMENT '確認時間' after audit_date;