package com.accelerate.acceleronServices.reservation.service;

import com.accelerate.acceleronServices.reservation.dto.request.ReservationDto;
import com.accelerate.acceleronServices.reservation.dto.response.ApiResponse;
import com.accelerate.acceleronServices.reservation.dto.response.GenericResponse;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;

import java.util.List;

public interface ReservationService {
    ApiResponse<GenericResponse> makeReservation(ReservationDto request);
    //List<ReservationEntity> getAll();
    List<ReservationEntity> getReservation(String name);
    List<ReservationEntity> getAllReservation();

    ApiResponse<GenericResponse> deleteReservation(String name);

    ApiResponse<GenericResponse> updateReservation(ReservationDto request);
}
