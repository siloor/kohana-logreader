			<div class="container-fluid">
				<div class="row-fluid">
					<div id="message-container" class="col-sm-9">
						<div id="message" class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">Message</h3>
							</div>
							<div class="panel-body">
								<?php if ($message === NULL): ?>
								The message doesn't exist.
								<?php else : ?>
								<h4><?php print htmlspecialchars($message['type']); ?></h4>
								<p><span class="label label-<?php print $message['style']; ?>"><?php print $message['level']; ?></span></p>
								<p><strong>Date:</strong> <?php print $message['date']; ?> <?php print $message['time']; ?></p>
								<p><strong>Message:</strong> <?php print htmlspecialchars($message['message']); ?></p>
								<p><strong>File:</strong> <?php print $message['file']; ?></p>
								<?php if (count($message['trace'])): ?>
								<p><strong>Trace:</strong>
								<?php for ($i = 0; $i < count($message['trace']); $i++): ?>
								<br /><strong><?php print $i; ?>:</strong> <?php print htmlspecialchars($message['trace'][$i]); ?>
								<?php endfor; ?>
								</p>
								<?php endif; ?>
								<p><strong>Raw:</strong> <?php print htmlspecialchars($message['raw']); ?></p>
							<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>