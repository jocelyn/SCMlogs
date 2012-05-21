#!/usr/bin/python
import ConfigParser
import sys
from string import replace, split;

# class Declaration
class SCMconfig:
	def __init__ (self, cfg_file, opt_repo):
		self._filename = cfg_file;
		cfg = ConfigParser.ConfigParser()
		try:
			cfg.read (cfg_file)
			self.load_options (cfg, opt_repo)
		except:
			self.error ("\n\n[!] Error while reading configuration from: \n%s\n" %(cfg_file) );

	def error (self, m):
		sys.stderr.write (m)
		sys.exit(2);

	def option (self, cfg, sect, name):
		try:
			return cfg.get (sect, name)
		except ConfigParser.NoSectionError:
			self.error ("\n\n[!] Error in config : section [%s] is missing.\n" %(sect) );
		except ConfigParser.NoOptionError:
			self.error ("\n\n[!] Error in config : option [%s] is missing.\n" %(name) );

	def remove_double_quotes (self, text):
		return replace (text, '"', '')

	def cfg_repo_section (self, repo):
		return "repo:%s" % (repo)

	def load_options (self, cfg, repo):
		# Global
		self.SCMlogs_appdir = self.option (cfg, "global", "SCMlogs_appdir")
		self.SCMlogs_appurl = self.remove_double_quotes (self.option (cfg, "global", "SCMlogs_appurl"))
		if cfg.has_option("global", "SCMlogs_datadir"):
			self.data_dir = self.option (cfg, "global", "SCMlogs_datadir")
		else:
			self.data_dir = "%sdata" % (self.SCMlogs_appdir)

		# SCM mode
		if len(repo) > 0:
			if cfg.has_section (self.cfg_repo_section (repo)):
				self.SCMrepository = repo
			else:
				self.error ("Repository id specified in argument [%s] is not valid\n" % (repo));
		else:
			if cfg.has_option("global", "SCM_repositories"):
				repositories = self.option (cfg, "global", "SCM_repositories")
				self.SCMrepository = repositories.split (',')[0]
			if cfg.has_option("global", "SCM_default_repository"):
				self.SCMrepository = self.option (cfg, "global", "SCM_default_repository")

		repo_section = self.cfg_repo_section (self.SCMrepository)
		self.SCMmode = self.option (cfg, repo_section, "mode")
		self.repository_path = self.option (cfg, repo_section, "repository_path")
		self.repository_name = self.option (cfg, repo_section, "repository_name")
		self.logs_dir = self.option (cfg, repo_section, "logs_dir")

		# Users data
		if cfg.has_section("users"):
			if cfg.has_option("users", "cfg_extension"):
				self.cfg_ext = self.option (cfg, "users", "cfg_extension")
			if cfg.has_option("users", "pref_extension"):
				self.pref_ext = self.option (cfg, "users", "pref_extension")
		else:
			self.cfg_ext = '.cfg';
			self.pref_ext = '.pref';

		self.cfg_dir = "%s/%s/%s/" % (self.data_dir, self.SCMmode, self.SCMrepository)

		# Browsing
		if cfg.has_option ("global", "browsing"):
			self.webapp_script = "scmbrowser/";
			self.browsing = self.option (cfg, "global", "browsing")
			self.webapp_url = self.remove_double_quotes (self.option (cfg, self.browsing, "webapp_url"))
		else:
			self.webapp_script = "scmbrowser/";
			self.browsing = ""
			self.webapp_url = "%s/%s" % (self.SCMlogs_appurl, self.webapp_script)
		# Email
		self.smtp_server = self.option (cfg, "email", "smtp_server")
		self.at_domain_name = self.option (cfg, "email", "at_domain_name")
		self.superuser_name = self.option (cfg, "email", "superuser_name")
		self.superuser_email = self.option (cfg, "email", "superuser_email")
		self.organization_name = self.option (cfg, "email", "organization_name")
