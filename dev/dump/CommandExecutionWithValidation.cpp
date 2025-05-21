/**
 * Executes a command with validation and error handling
 * 
 * @param this Pointer to Command instance
 * @param origin Pointer to CommandOrigin containing execution context
 * @param output Pointer to CommandOutput for message handling
 */
void __fastcall Command::run(
    Command* this,
    const CommandOrigin* origin,
    CommandOutput* output
) {
    // Store important pointers for later use
    Command* cmdInstance = this;
    
    // Check if command flags are valid for this origin
    bool originFlagsValid = CommandRegistry::checkOriginCommandFlags(
        this->registry,
        origin,
        this->commandFlags,
        this->permissionLevel
    );

    if (!originFlagsValid) {
        handleInvalidCommandFlags(this, origin, output);
        return;
    }

    // Get level and check if it's initialized
    auto level = origin->getLevel();
    if (level && level->isInitialized()) {
        // Check if command is enabled in editor
        if (!isCommandEnabledInEditor(this, output)) {
            handleDisabledInEditor(this, origin, output);
            return;
        }

        // Execute the actual command implementation
        (this->*executeImpl)(origin, output);
        sendTelemetry(this, origin, output);
        return;
    }

    // Handle education edition specific logic
    if (shouldHandleEducationEdition(level)) {
        if (handleEducationEditionRestrictions(this, origin, output)) {
            return;
        }
    }

    // Handle unknown command case
    handleUnknownCommand(this, origin, output);
}

/**
 * Checks if command is enabled in editor mode
 */
private bool isCommandEnabledInEditor(Command* cmd, CommandOutput* output) {
    ContentTierIncompatibleReason reason(cmd->contentTier);
    std::string symbolString;
    cmd->registry->symbolToString(&symbolString, &reason);
    
    return CommandRegistry::enabledInEditor(cmd->registry, &symbolString);
}

/**
 * Handles case when command is disabled in editor
 */
private void handleDisabledInEditor(
    Command* cmd,
    const CommandOrigin* origin,
    CommandOutput* output
) {
    // Add disabled message
    std::vector<CommandOutputParameter> params;
    params.push_back(cmd->getName());
    
    output->addMessage(
        "commands.generic.disabled.editorLocked",
        params,
        CommandOutput::Error
    );
    
    // Send telemetry about failed command
    cmd->sendTelemetry(origin, output);
}

/**
 * Handles case when command flags are invalid
 */
private void handleInvalidCommandFlags(
    Command* cmd,
    const CommandOrigin* origin,
    CommandOutput* output
) {
    std::string cmdName;
    ContentTierIncompatibleReason reason(cmd->contentTier);
    cmd->registry->symbolToString(&cmdName, &reason);

    std::vector<CommandOutputParameter> params;
    params.push_back(cmdName);

    output->addMessage(
        "commands.generic.unknown",
        params,
        CommandOutput::Error
    );

    cmd->sendTelemetry(origin, output);
}

/**
 * Checks if education edition handling is needed
 */
private bool shouldHandleEducationEdition(Level* level) {
    return level && level->hasEduFeatures();
}

/**
 * Handles education edition specific restrictions
 * Returns true if command execution should stop
 */
private bool handleEducationEditionRestrictions(
    Command* cmd,
    const CommandOrigin* origin,
    CommandOutput* output
) {
    auto eduSettings = origin->getLevel()->getEducationLevelSettings();
    if (!eduSettings || !eduSettings->isFeatureEnabled()) {
        return false;
    }

    // Check education edition restrictions
    if (isRestrictedInEducationEdition(cmd)) {
        std::vector<CommandOutputParameter> params;
        params.push_back(cmd->getName());
        
        output->addMessage(
            "commands.generic.blocked_edu",
            params,
            CommandOutput::Error
        );
        
        cmd->sendTelemetry(origin, output);
        return true;
    }

    return false;
}