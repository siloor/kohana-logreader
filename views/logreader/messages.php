		<div class="container-fluid">
			<div class="row-fluid">
				<div class="col-sm-9">
					<form class="form-horizontal">
						<fieldset>
							<legend>Messages</legend>
							<div class="form-group">
								<label for="input-message" class="col-lg-1 control-label">Message<span class="glyphicon glyphicon-info-sign" id="message-tooltip" title="Regular expression"></span></label>
								<div class="col-lg-6">
									<input type="text" class="form-control <?php if (!$filters['message']['valid']) print 'error'; ?>" id="input-message" name="message" placeholder="Message" value="<?php print $filters['message']['text'] ?>" />
								</div>
							</div>
							<div class="form-group">
								<label for="input-date-from" class="col-lg-1 control-label">Date from</label>
								<div class="col-lg-2">
									<input type="text" class="form-control" id="input-date-from" name="date-from" placeholder="Date from" value="<?php print $filters['date-from']; ?>" />
								</div>
								<label for="input-date-to" class="col-lg-1 control-label">Date to</label>
								<div class="col-lg-2">
									<input type="text" class="form-control" id="input-date-to" name="date-to" placeholder="Date to" value="<?php print $filters['date-to']; ?>" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-1 control-label">Levels</label>
								<?php $level_index = 0; foreach (LogReader::$levels as $level): $level_index++; ?>
								<?php if (($level_index % 3) === 1): ?>
								<div class="col-lg-2">
								<?php endif; ?>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="levels[]" value="<?php print $level; ?>" <?php if (in_array($level, $filters['levels'])) print 'checked=""'; ?>> <?php print $level; ?>
										</label>
									</div>
								<?php if (($level_index % 3) === 0 || $level_index === count(LogReader::$levels)): ?>
								</div>
								<?php endif; ?>
								<?php endforeach; unset($level_index); ?>
							</div>
							<div class="form-group">
								<div class="col-lg-6 col-lg-offset-1">
									<button type="submit" class="btn btn-primary">Run filter</button>
								</div>
							</div>
						</fieldset>
					</form>
					<table id="logs" cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">
						<thead>
							<tr>
								<th>Date</th>
								<th>Level</th>
								<th>Message</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($messages as $message): ?>
							<tr data-data="<?php print htmlspecialchars(json_encode($message)); ?>">
								<td class="date"><?php print $message['date'] . ' ' . $message['time']; ?></td>
								<td class="level"><span class="label label-<?php print $message['style']; ?>"><?php print $message['level']; ?></span></td>
								<td class="message"><div class="outer"><div><?php print $message['type']; ?> - <?php print $message['message']; ?></div></div></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<div class="text-center">
						<ul class="pagination">
							<?php foreach ($pages as $page): ?>
							<li class="<?php print (int) $page['title'] === $current_page ? 'active' : (isset($page['url']) ? '' : 'disabled'); ?>">
								<a href="<?php print !isset($page['url']) || (int) $page['title'] === $current_page ? 'javascript:void(0);' : $page['url']; ?>"><?php print ($page['title'] === 'previous' ? '&laquo;' : ($page['title'] === 'next' ? '&raquo;' : $page['title'])); ?></a>
							</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
				<div id="message-container" class="col-sm-3">
					<div id="message" class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Message</h3>
						</div>
						<div class="panel-body">No message selected.</div>
					</div>
				</div>
			</div>
		</div>