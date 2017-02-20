#!/usr/bin/python
# -*- coding: UTF-8 -*-
import time
import pymysql
import random


def statistic_customers(cursor) :
    try:
        cursor.execute("delete from `crm_sales_customer_statistic`")
        sql ="insert into `crm_sales_customer_statistic`(`customer_total`, `number_of_waiting_touch`, `number_of_touching`," \
             "`number_of_cooperating`, `number_of_done`, `number_of_potential`, `number_of_general`, `number_of_important`, `number_of_ka`," \
             "`sales_uuid`, `sales_name`) " \
             "select count(t1.`customer_uuid`) customer_total," \
             "sum(case WHEN t3.`status` = 1 then 1 else 0 end)  number_of_waiting_touch," \
             "sum(case WHEN t3.`status` = 2 then 1 else 0 end)  number_of_touching," \
             "sum(case WHEN t3.`status` = 3 then 1 else 0 end)  number_of_cooperating," \
             "sum(case WHEN t3.`status` = 4 then 1 else 0 end)  number_of_done," \
             "sum(case WHEN t1.`level` = 5 then 1 else 0 end)  number_of_potential," \
             "sum(case WHEN t1.`level` = 6 then 1 else 0 end)  number_of_general," \
             "sum(case WHEN t1.`level` = 7 then 1 else 0 end)  number_of_important," \
             "sum(case WHEN t1.`level` = 8 then 1 else 0 end)  number_of_ka, " \
             "t2.`uuid` sales_uuid, t2.`name` sales_name " \
             "from `crm_customer_advance` t1 " \
             "LEFT  JOIN  `hr_employee_basic_information` t2 on t1.`sales_uuid` = t2.`uuid` " \
             "LEFT JOIN `crm_customer_basic` t3 on t1.`customer_uuid` = t3.`uuid` " \
             "WHERE t3.`enable` = 127 " \
             "group by t1.sales_uuid"

        # 更新客户统计表
        cursor.execute(sql)

        # 统计最近7天，30天的跟进次数
        sql = "SELECT t1.`uuid`, " \
                       "sum(CASE WHEN t2.`time` > UNIX_TIMESTAMP() - 604800 then 1 else 0 end) number_of_last_7_days_touch_records,"\
                       "sum(CASE WHEN t2.`time` > UNIX_TIMESTAMP() - 2592000 then 1 else 0 end) number_of_last_30_days_touch_records "\
                       "from `hr_employee_basic_information` t1 LEFT JOIN `crm_touch_record` t2 ON t2.`created_uuid` = t1.`uuid` "\
                       "group by t1.`uuid`"
        cursor.execute(sql)
        records = cursor.fetchall()
        # 将其更新到数据库里面去
        for record in records :
            sql = "update `crm_sales_customer_statistic` set " \
                  "`number_of_last_7_days_touch_records` = %d, "\
                  "`number_of_last_30_days_touch_records` = %d "\
                  "WHERE `sales_uuid` = '%s'" % (record[1], record[2], record[0])
            cursor.execute(sql)
    except Exception as e:
        print (e)
        return False

    return True


from random import Random
def random_str(random_length=4):
    str = ''
    chars = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789'
    length = len(chars) - 1
    random = Random()
    for i in range(random_length):
        str+=chars[random.randint(0, length)]
    return str


def statistic_sales_anniversary_achievement(cursor) :
    yearTimeStamp = {
        2015: [
            time.mktime(time.strptime('2015-1-1 00:00:00', '%Y-%m-%d %H:%M:%S')),
            time.mktime(time.strptime('2015-12-31 23:59:59', '%Y-%m-%d %H:%M:%S')),
        ],
        2016: [
            time.mktime(time.strptime('2016-1-1 00:00:00', '%Y-%m-%d %H:%M:%S')),
            time.mktime(time.strptime('2016-12-31 23:59:59', '%Y-%m-%d %H:%M:%S')),
        ],
        2017: [
            time.mktime(time.strptime('2017-1-1 00:00:00', '%Y-%m-%d %H:%M:%S')),
            time.mktime(time.strptime('2017-12-31 23:59:59', '%Y-%m-%d %H:%M:%S')),
        ],
        2018: [
            time.mktime(time.strptime('2018-1-1 00:00:00', '%Y-%m-%d %H:%M:%S')),
            time.mktime(time.strptime('2018-12-31 23:59:59', '%Y-%m-%d %H:%M:%S')),
        ],
        2019: [
            time.mktime(time.strptime('2019-1-1 00:00:00', '%Y-%m-%d %H:%M:%S')),
            time.mktime(time.strptime('2019-12-31 23:59:59', '%Y-%m-%d %H:%M:%S')),
        ],
        2020: [
            time.mktime(time.strptime('2020-1-1 00:00:00', '%Y-%m-%d %H:%M:%S')),
            time.mktime(time.strptime('2020-12-31 23:59:59', '%Y-%m-%d %H:%M:%S')),
        ]
    }

    try:
        # 先将旧的数据全部清0,然后在跟新新的数据
        cursor.execute('update crm_sales_anniversary_achievement_statistic set `achieved`=0, `received_money`=0,`checked_stamp_money`=0')
        for key in yearTimeStamp:
            if time.time() < yearTimeStamp[key][0]:
                continue

            sql = 'select  t1.sales_uuid, SUM(t3.actual_money_amount) achieved, ' \
                  'SUM(t3.received_money) received_money, SUM(t3.checked_stamp_money) checked_stamp_money ' \
                  'from crm_customer_advance t1 ' \
                  'LEFT JOIN crm_customer_project_map t2 on t1.customer_uuid = t2.customer_uuid ' \
                  'LEFT JOIN crm_project t3 on t2.project_uuid = t3.uuid ' \
                  'WHERE t3.`status` in (2,3) and t3.start_time > %d AND t3.start_time <= %d ' \
                  'AND t3.`enable` = 1 ' \
                  'GROUP BY t1.sales_uuid ' % (yearTimeStamp[key][0], yearTimeStamp[key][1])
            cursor.execute(sql)

            records = cursor.fetchall()
            for item in records:
                sql = "select uuid from crm_sales_anniversary_achievement_statistic where `sales_uuid`='%s' and `year` = %d"\
                      % (item[0], key)
                cursor.execute(sql)
                record = cursor.fetchone()
                if not record:
                    uuid = str(time.time()) + random_str(4)
                    # 表示数据里面没有
                    insertSql = 'insert into crm_sales_anniversary_achievement_statistic(`uuid`, `sales_uuid`, `year`, `achieved`,' \
                                '`received_money`,`checked_stamp_money`) VALUES ("%s", "%s", %d, %f, %f, %f)' \
                                % (uuid, item[0], key, item[1], item[2], item[3])
                    cursor.execute(insertSql)
                    continue

                updateSql = 'update `crm_sales_anniversary_achievement_statistic` set `achieved` = %f, ' \
                            '`received_money` = %f, `checked_stamp_money` = %f WHERE uuid = "%s"' \
                            % (item[1], item[2], item[3], record[0])
                cursor.execute(updateSql)
        # 统计一下客户总数
        cursor.execute('UPDATE crm_sales_anniversary_achievement_statistic t1 '
                       'INNER JOIN crm_sales_customer_statistic t2 on t1.sales_uuid = t2.sales_uuid '
                       'set t1.customer_total = t2.customer_total')
    except Exception as e:
        print (e)
        return False

    return True


def statistic_projects(cursor):
    try:
        cursor.execute('delete from `crm_project_statistic`')
        sql = 'INSERT INTO crm_project_statistic(`projects_total`, `number_of_touching`, ' \
              '`number_of_executing`, `number_of_done`, `number_of_failed`, `manager_uuid`, ' \
              '`number_of_last_7_days_touch_records`, `number_of_last_30_days_touch_records`) ' \
              'SELECT COUNT(t1.uuid) projects_total,  SUM(CASE WHEN t1.`status` = 1 THEN 1 ELSE 0 END) number_of_touching,' \
              'SUM(CASE WHEN t1.`status` = 2 THEN 1 ELSE 0 END) number_of_executing,' \
              'SUM(CASE WHEN t1.`status` = 3 THEN 1 ELSE 0 END) number_of_done,' \
              'SUM(CASE WHEN t1.`status` = 4 THEN 1 ELSE 0 END) number_of_failed, t1.project_manager_uuid manager_uuid,' \
              'SUM(CASE WHEN t3.`time` > UNIX_TIMESTAMP() - 604800 then 1 else 0 end) number_of_last_7_days_touch_records,' \
              'SUM(CASE WHEN t3.`time` > UNIX_TIMESTAMP() - 2592000 then 1 else 0 end) number_of_last_30_days_touch_records ' \
              'FROM crm_project  t1 LEFT JOIN crm_project_touch_record_map t2 on t1.uuid = t2.project_uuid ' \
              'LEFT JOIN crm_touch_record t3 on t3.uuid = t2.touch_record_uuid ' \
              'WHERE t1.`enable` = 1 ' \
              'GROUP BY t1.`project_manager_uuid`'

        cursor.execute(sql)
    except Exception as e:
        print (e)
        return False

    return True


def statistic_project_anniversary_achievement(cursor) :
    year_time_stamp = {
        2015: [
            time.mktime(time.strptime('2015-1-1 00:00:00', '%Y-%m-%d %H:%M:%S')),
            time.mktime(time.strptime('2015-12-31 23:59:59', '%Y-%m-%d %H:%M:%S')),
        ],
        2016: [
            time.mktime(time.strptime('2016-1-1 00:00:00', '%Y-%m-%d %H:%M:%S')),
            time.mktime(time.strptime('2016-12-31 23:59:59', '%Y-%m-%d %H:%M:%S')),
        ],
        2017: [
            time.mktime(time.strptime('2017-1-1 00:00:00', '%Y-%m-%d %H:%M:%S')),
            time.mktime(time.strptime('2017-12-31 23:59:59', '%Y-%m-%d %H:%M:%S')),
        ],
        2018: [
            time.mktime(time.strptime('2018-1-1 00:00:00', '%Y-%m-%d %H:%M:%S')),
            time.mktime(time.strptime('2018-12-31 23:59:59', '%Y-%m-%d %H:%M:%S')),
        ],
        2019: [
            time.mktime(time.strptime('2019-1-1 00:00:00', '%Y-%m-%d %H:%M:%S')),
            time.mktime(time.strptime('2019-12-31 23:59:59', '%Y-%m-%d %H:%M:%S')),
        ],
        2020: [
            time.mktime(time.strptime('2020-1-1 00:00:00', '%Y-%m-%d %H:%M:%S')),
            time.mktime(time.strptime('2020-12-31 23:59:59', '%Y-%m-%d %H:%M:%S')),
        ]
    }

    try:
        # 先将旧的数据全部清0,然后在跟新新的数据
        cursor.execute('update `crm_project_anniversary_achievement_statistic` set `achieved`=0, `received_money`=0,`checked_stamp_money`=0')
        for key in year_time_stamp:
            if time.time() < year_time_stamp[key][0]:
                continue
            sql = 'select t1.project_manager_uuid manager_uuid, SUM(t1.`actual_money_amount`) achieved, ' \
                  'SUM(t1.received_money) received_money, SUM(t1.checked_stamp_money) checked_stamp_money ' \
                  'FROM crm_project t1  WHERE t1.`status` in (2,3) and t1.start_time > %d AND t1.start_time <= %d ' \
                  'AND t1.`enable` = 1 ' \
                  'GROUP BY t1.project_manager_uuid'  % (year_time_stamp[key][0], year_time_stamp[key][1])
            cursor.execute(sql)

            records = cursor.fetchall()
            for item in records:
                sql = "SELECT uuid FROM crm_project_anniversary_achievement_statistic WHERE `manager_uuid`='%s' AND `year` = %d"\
                      % (item[0], key)
                cursor.execute(sql)
                record = cursor.fetchone()
                if not record:
                    uuid = str(time.time()) + random_str(4)
                    # 表示数据里面没有
                    insert_sql = 'insert into crm_project_anniversary_achievement_statistic(`uuid`, `manager_uuid`, `year`, `achieved`,' \
                                '`received_money`,`checked_stamp_money`) VALUES ("%s", "%s", %d, %f, %f, %f)' \
                                % (uuid, item[0], key, item[1], item[2], item[3])
                    cursor.execute(insert_sql)
                    continue

                update_sql = 'update `crm_project_anniversary_achievement_statistic` set `achieved` = %f, ' \
                            '`received_money` = %f, `checked_stamp_money` = %f WHERE uuid = "%s"' \
                            % (item[1], item[2], item[3], record[0])
                cursor.execute(update_sql)
        # 统计一下客户总数
        cursor.execute('UPDATE crm_project_anniversary_achievement_statistic t1 '
                       'INNER JOIN crm_project_statistic t2 on t1.manager_uuid = t2.manager_uuid '
                       'set t1.projects_total = t2.projects_total')
    except Exception as e:
        print (e)
        return False

    return True


while 1:
    db = pymysql.connect("rdsvwq3t80d6dyy39hk3o.mysql.rds.aliyuncs.com", "qmg_erp", "Qmg2016mw#sem", "qmg_erp_prod")
    cursor = db.cursor()
    if statistic_customers(cursor) is False:
        db.rollback()
        print ('statistic_customers die')
        break
    if statistic_sales_anniversary_achievement(cursor) is False :
        db.rollback()
        print ('statistic_sales_anniversary_achievement die')
        break
    if statistic_projects(cursor) is False:
        db.rollback()
        print ('statistic_projects die')
        break
    if statistic_project_anniversary_achievement(cursor) is False:
        db.rollback()
        print ('statistic_project_anniversary_achievement die')
        break

    db.commit()
    db.close()
    time.sleep(86400)
