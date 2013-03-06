


# this Makefile creates a .zip archive from this repository,
# suitable to be uploaded to the Wordpress installer, or extracted to wp-content/plugins

BASENAME=se3_plugin

# this has to be called feeligo because it will be the name of the extracted dir
TEMPDIR=feeligo_giftbar_se3
# this is for initially cloning the git repository
TEMPDIRCLONE=feeligo_clone

TAG=$(shell git for-each-ref --format="%(refname)" --sort=-taggerdate --count=1 refs/tags | head -n 1 | sed -e s:refs/tags/::)
LASTCOMMIT=$(shell git log -1 | head -n 1 | grep ^commit.*$ | sed s/commit.//)
LASTCOMMITOFTAG=$(shell git show $(TAG) | grep ^commit.*$ | head -n 1 | sed s/commit.//)

ifeq ($(TAG),)
ARCHIVE="$(BASENAME)"
else
ARCHIVE="$(BASENAME)_$(TAG)"
endif



.PHONY: default
default: compress
	@echo "Packaged to $(ARCHIVE)"


compress: copy
	@echo "Compressing..."
	rm -f $(ARCHIVE).tar
	tar vcf $(ARCHIVE).tar $(TEMPDIR)
	rm -rf $(TEMPDIR)


copy: check
	@echo "Cloning git repo to temp directory $(TEMPDIRCLONE)..."
	$(shell mkdir $(TEMPDIRCLONE))
	git clone --recursive . $(TEMPDIRCLONE)
	@echo "Copying files to temp directory $(TEMPDIR)..."
	$(shell mkdir $(TEMPDIR))
	rsync -r --exclude '.*' --exclude Makefile "./$(TEMPDIRCLONE)/" $(TEMPDIR)
	rm -rf $(TEMPDIRCLONE)


.PHONY: clean
clean:
	@echo "Cleaning up..."
	rm -rf $(TEMPDIR)
	rm -rf $(TEMPDIRCLONE)


.PHONY: check
check:
ifeq ($(TAG),)
# if the latest tag is empty, show warning
	@echo "\n\t** WARNING **\n\t'git tag' returned an empty output.\n\tRemember to tag your work with an incremented version number before packaging!\n"
else
ifeq ($(LASTCOMMIT),$(LASTCOMMITOFTAG))
# if the latest commit is tagged, go ahead
else
# if the latest commit is not tagged, show warning
	@echo "\n\t** WARNING **\n\tYour latest commit ($(LASTCOMMIT)) does not seem to be tagged.\n\tRemember to tag your work with an incremented version number before packaging! (latest tag is '$(TAG))'\n"
endif
endif	