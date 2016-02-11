<div class="message_overview">
    <div class="message_list_wrapper">
        <div class="message_list">
            <? while ($message = $inbox->fetch(PDO::FETCH_ASSOC)): ?>
                <? $n = true ?>
                <a href="<?= PluginEngine::GetURL('MessageOverview', array(), 'show/message/' . $message['message_id']) ?>">

                    <date><?= strftime('%x %X', $message['mkdate']) ?></date>
                    <p class="author">
                        <?= $message['autor_id'] != "____%system%____" ? User::find($message['autor_id'])->getFullname() : _("Systemnachricht") ?>
                    </p>
                    <?= htmlReady($message['topic']) ?>
                </a>
            <? endwhile; ?>

            <p class="no_messages" style="display: <?= $n ? 'none' : 'inherit' ?>"><?= _('Keine Nachrichten') ?></p>
        </div>
    </div>
    <div class="message_display_wrapper">
        <div class="message_display">
        </div>
    </div>
</div>