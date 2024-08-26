# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.1.0] 2024-08-26

### Added

- Support for objects that contain array lists

## [3.0.0] 2024-07-30

### Added

- Support for updating data using MutableData

## [2.1.0] 2023-11-15

### Added

- Set a default for DataProxyBehavior source

## [2.0.0] 2023-11-15

### Changed

- All implementations of Data are now `readonly`
- KeyedData constructor changed from `private` to `public`

## [1.2.0] 2023-11-11

### Added

- JsonData, a proxy that wraps KeyedData from JSON strings and PSR-7 requests and responses

## [1.1.0] 2023-11-06

### Added

- DataProxyBehavior, a helper trait for implementation of DataProxy

## [1.0.0] 2023-11-03

### Added

- Data interface
- KeyedData, a basic implementation of Data
- DataProxy, an abstract proxy of Data for extension
