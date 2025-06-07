<?php

namespace Smug\Core\Cache\Backend;

/**
 * A contract for a cache backends which store variables in volatile
 * memory and as such support receiving any variable type to store.
 *
 * Note: respect for this contract is up to each individual frontend.
 * The contract can be respected for a small performance boost, but
 * the result is marginal except for cases with huge serialized
 * data sets.
 *
 * Respected by the VariableFrontend which checks if the backend
 * has this interface, in which case it allows the backend to store
 * the value directly without serializing it to a string, and does
 * not attempt to unserialize the string on every get() request.
 */
interface TransientBackendInterface extends BackendInterface {}
