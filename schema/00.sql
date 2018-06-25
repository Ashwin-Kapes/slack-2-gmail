
--
--      DATABASES EXTENSIONS
--
CREATE EXTENSION pgcrypto;
CREATE EXTENSION hstore;

--
--      updated_at columd update
--
CREATE OR REPLACE FUNCTION updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';


--
--      USERS
--
CREATE TABLE users (
    id UUID PRIMARY KEY NOT NULL DEFAULT gen_random_uuid(),
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    gmail_account_id VARCHAR(255) NOT NULL UNIQUE,
    gmail_avatar VARCHAR(255) DEFAULT NULL,
    gmail_access_token VARCHAR(255) DEFAULT NULL,
    gmail_refresh_token VARCHAR(255) DEFAULT NULL,
    gmail_expires_at INTEGER DEFAULT NULL,
    flags JSONB DEFAULT '{}'::jsonb NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_users_email ON users (email);
CREATE INDEX idx_users_gmail_account_id ON users (gmail_account_id);

CREATE TRIGGER tgr_users_modtime BEFORE UPDATE ON users FOR EACH ROW EXECUTE PROCEDURE updated_at_column();


--
--      SLACK_ORGS
--
CREATE TABLE slack_orgs (
    id UUID PRIMARY KEY NOT NULL DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id) NOT NULL,
    name VARCHAR(255) NOT NULL,
    domain VARCHAR(255) NOT NULL,
    icon VARCHAR(255) NOT NULL,
    api_key VARCHAR(255) NOT NULL,
    synced_at TIMESTAMP WITH TIME ZONE DEFAULT NULL,
    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_slack_orgs_user_id ON slack_orgs (user_id);
CREATE INDEX idx_slack_orgs_synced_at ON slack_orgs (synced_at);

CREATE TRIGGER tgr_slack_orgs_modtime BEFORE UPDATE ON slack_orgs FOR EACH ROW EXECUTE PROCEDURE updated_at_column();

--
--      SLACK_CHANNELS
--
CREATE TABLE slack_channels (
    id UUID PRIMARY KEY NOT NULL DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id) NOT NULL,
    slack_org_id UUID REFERENCES slack_orgs(id) NOT NULL,
    channel_id VARCHAR(16) NOT NULL,
    name VARCHAR(255) NOT NULL,
    is_member BOOLEAN NOT NULL,
    is_archived BOOLEAN NOT NULL,
    is_private BOOLEAN NOT NULL,
    num_members INTEGER NOT NULL,
    is_backup_enabled BOOLEAN DEFAULT FALSE,  -- ready to scrape
    sync_cursor FLOAT NOT NULL DEFAULT 0,
    synced_at TIMESTAMP WITH TIME ZONE DEFAULT NULL,
    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);

CREATE UNIQUE INDEX idx_slack_channels_unique ON slack_channels (user_id, slack_org_id, channel_id);
CREATE INDEX idx_slack_channels_user_id ON slack_channels (user_id);
CREATE INDEX idx_slack_channels_slack_org_id ON slack_channels (slack_org_id);
CREATE INDEX idx_slack_channels_channel_id ON slack_channels (channel_id);
CREATE INDEX idx_slack_channels_is_backup_enabled ON slack_channels (is_ready, is_backup_enabled);

CREATE TRIGGER tgr_slack_channels_modtime BEFORE UPDATE ON slack_channels FOR EACH ROW EXECUTE PROCEDURE updated_at_column();
