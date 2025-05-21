/**
 * Clears all effects from target actors
 * 
 * @param this Pointer to EffectCommand instance
 * @param origin Command origin
 * @param output Command output handler
 */
void __fastcall EffectCommand::clear(
    EffectCommand* this,
    const CommandOrigin* origin,
    CommandOutput* output
) {
    // Get selected actors
    std::vector<Actor*> selectedActors;
    CommandSelector<Actor>::results((char*)this + 32, &selectedActors, origin);
    
    if (!Command::checkHasTargets<Actor>(&selectedActors, output)) {
        cleanupActorResults(&selectedActors);
        return;
    }

    std::vector<Actor*> actorsWithEffectsRemoved;
    
    // Process each selected actor
    auto actorIter = selectedActors.begin();
    auto actorEnd = selectedActors.end();
    
    while (actorIter != actorEnd) {
        Actor* currentActor = *actorIter;
        
        if (ActorClassTree::isMob(currentActor->getEntityTypeId())) {
            if (currentActor->hasAnyEffects()) {
                // Remove effects and track actor
                currentActor->removeAllEffects();
                actorsWithEffectsRemoved.push_back(currentActor);
            } else {
                // Report no active effects
                reportNoActiveEffects(output, currentActor);
                addActorToResultList(output, currentActor);
            }
        }
        
        ++actorIter;
    }

    // Report success if any effects were removed
    if (!actorsWithEffectsRemoved.empty()) {
        reportEffectsRemoved(output, actorsWithEffectsRemoved);
    }

    // Cleanup
    cleanupIterators(actorIter, actorEnd);
    cleanupVectors(selectedActors, actorsWithEffectsRemoved);
}

/**
 * Reports that an actor has no active effects
 */
private void reportNoActiveEffects(CommandOutput* output, Actor* actor) {
    std::vector<CommandOutputParameter> params;
    params.emplace_back(actor);
    
    output->error(
        "commands.effect.failure.notActive.all",
        params
    );
}

/**
 * Adds actor to the result list with "player" type
 */
private void addActorToResultList(CommandOutput* output, Actor* actor) {
    std::string playerType = "player";
    output->addToResultList(playerType, actor);
}

/**
 * Reports successful removal of effects
 */
private void reportEffectsRemoved(
    CommandOutput* output,
    const std::vector<Actor*>& actors
) {
    std::vector<CommandOutputParameter> params;
    params.emplace_back(actors);
    
    output->success(
        "commands.effect.success.removed.all",
        params
    );
}

/**
 * Cleans up iterator resources
 */
private void cleanupIterators(SelectorIterator<Actor>& iter, SelectorIterator<Actor>& end) {
    cleanupIterator(iter);
    cleanupIterator(end);
}

/**
 * Cleans up a single iterator
 */
private void cleanupIterator(SelectorIterator<Actor>& iterator) {
    if (auto ptr = iterator.getInternalPtr()) {
        if (InterlockedDecrementRelease(&ptr->refCount) == 1) {
            ptr->destroy();
            if (InterlockedDecrementRelease(&ptr->secondaryRefCount) == 1) {
                ptr->cleanup();
            }
        }
    }
}

/**
 * Cleans up vector resources
 */
private void cleanupVectors(
    std::vector<Actor*>& selected,
    std::vector<Actor*>& affected
) {
    cleanupVector(selected);
    cleanupVector(affected);
}

/**
 * Cleans up a vector's resources
 */
private void cleanupVector(std::vector<Actor*>& vec) {
    if (!vec.empty()) {
        if (vec.capacity() >= 0x1000 / sizeof(Actor*)) {
            auto ptr = reinterpret_cast<void*>(
                reinterpret_cast<char*>(vec.data()) - 8
            );
            if (reinterpret_cast<char*>(vec.data()) - 
                reinterpret_cast<char*>(ptr) - 8 > 0x1F) {
                _invalid_parameter_noinfo_noreturn();
            }
            std::_Return_temporary_buffer<unsigned int>(ptr);
        } else {
            std::_Return_temporary_buffer<unsigned int>(vec.data());
        }
        vec.clear();
    }
}