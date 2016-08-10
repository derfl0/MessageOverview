<div class="messages">
<h2><?= htmlReady($topic) ?></h2>
<? if ($id): ?>
    <a href="<?= URLHelper::getLink("dispatch.php/messages/write", array('answer_to' => $id)) ?>"
       data-dialog="buttons"><?= \Studip\Button::create(_("Antworten")) ?></a>
    <a href="<?= URLHelper::getLink("dispatch.php/messages/write", array('answer_to' => $id, 'quote' => $id)) ?>"
       data-dialog="buttons"><?= \Studip\Button::create(_("Zitieren")) ?></a>
<? endif; ?>
<? foreach ($messages->fetchAll(PDO::FETCH_ASSOC) as $msg): ?>
    <article class="message" id="<?= $msg['id'] ?>">
        <header>
            <?= $msg['autor_id'] != "____%system%____" ? ObjectdisplayHelper::avatarlink(User::find($msg['autor_id'])) : _("Systemnachricht") ?>
            <date>
                <?= strftime('%x %X', $msg['mkdate']) ?>
            </date>
        </header>
        <?= formatReady($msg['message']) ?>
    </article>
<? endforeach; ?>
</div>
<div class="message_answer">
    <textarea name="answer_text"></textarea>
    <?= \Studip\Button::create(_('Absenden')) ?>
</div>