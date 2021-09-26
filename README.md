# symfony_rabbitmq_project (symfony 5.3, php 7.4, rabbitmq, docker)
<p><b>RabbitMQ запускается в docker: docker-compose up -d</b></p>
<p><b>Создание администратора командой: php bin/console app:create-admin</b></p>
 
<p>Задача:</p>
<p>Необходимо реализовать систему уведомлений для посетителей фитнес-клуба.</p>
<p>Описание:</p>
У клиентов фитнес-клуба должна быть возможность подписаться на уведомления о групповых
занятиях. Это может быть информация об отмене/переносе занятия, смены тренера и др.
Уведомления могут приходить как на почту, так и на телефон (СМС), в зависимости от того, что
выберет клиент.
Клиентов в базу данных добавляет администратор. При добавлении нового клиента
администратору необходимо заполнить следующие поля:
<p>- ФИО</p>
<p>- Дата рождения</p>
<p>- Пол</p>
<p>- Email (авторизация по этому полю)</p>
<p>- Телефон</p>
<p>- Фото клиента (необязательное поле)</p>
Клиент в базе по умолчанию создается в статусе «Не подтвержден». В этом статусе клиент не
может войти в систему. После создания аккаунта на почту клиента приходит ссылка для смены
пароля. После того, как клиент переходит по ссылке, вводит пароль и подтверждение пароля, его
аккаунт автоматически переходит в статус «Подтвержден», и клиент попадает в свой профиль.
На странице ему доступен список всех групповых занятий с указанием, на какие из них он
подписан и способ подписки (по почте или телефону). Клиент может оформить уведомления на
выбранные занятия, отметив их мышкой. Уведомление об одном занятии может приходить
только либо по телефону, либо на почту. Клиент может изменить способ уведомления для
занятия, или отказаться от него вообще.
В своём профиле клиент должен видеть все свои данные, но изменять может только пароль.
Администратору доступен список всех клиентов в системе. Помимо добавления, администратор
может редактировать профиль клиента или блокировать/разблокировать его. Поля для
редактирования те же, что и при добавлении (кроме email). Если клиент заблокирован, то он не
может войти в систему, а также не получает никакие уведомления.
Администратору доступен список всех групповых занятий. Он может создать новое групповое
занятие или отредактировать/удалить существующее.
Список полей для группового занятия:
<p>- Название</p>
<p>- ФИО тренера</p>
<p>- Описание</p>
Также администратор в списке занятий должен видеть количество клиентов, подписанных на
каждое занятие.

На странице просмотра группового занятия, помимо информации о самом занятии, для
администратора доступен блок для отправки уведомлений. Он состоит из двух полей для ввода
сообщений (одно для почтовых, другое для СМС) и кнопки «Отправить».
Должна быть предусмотрена возможность использовать в сообщениях шаблонные переменные,
такие как ФИО, дата рождения, email или телефон (например, «Здравствуйте, %name%!»).
Подмена шаблонных переменных осуществляется непосредственно перед отправкой клиенту.
После нажатия на кнопку «Отправить» сообщения должны попадать в очередь на обработку.
Информацию о сообщениях в базе данных сохранять не нужно.

Предположим, что отправка СМС работает по такому принципу. На определенный адрес по HTTP
протоколу в адресе передается номер телефона и сообщение.
Например: http://domain.ru/?phone=+79876543210&amp;message=Сообщение
В случае успешной отправки сообщения вернется HTTP код 200. В случае ошибки другой код.
Оформление задачи:
1) Систему необходимо реализовать на фреймворке Symfony с использованием брокера
RabbitMQ;
2) Необходимо предусмотреть повторную отправку СМС сообщений. Это должно
происходить не ранее, чем через 10 минут. И так до тех пор, пока сообщение не будет
отправлено. Этот механизм необходимо реализовать через отдельную очередь
сообщений.
3) Приложение должно соответствовать требованиям безопасности web-приложений;
4) Внутри кода необходимы поясняющие комментарии;
