package com.accelerate.acceleronServices.reservation.service;

import com.accelerate.acceleronServices.reservation.dto.request.ReservationRequestDto;
import com.accelerate.acceleronServices.reservation.dto.response.ApiResponse;
import com.accelerate.acceleronServices.reservation.dto.response.GenericResponse;
import com.accelerate.acceleronServices.reservation.model.ReservationEntity;


import java.util.List;
import java.util.Optional;

public interface ReservationRequestService {
    ApiResponse<GenericResponse> makeReservation(ReservationRequestDto request);

    List<ReservationEntity> getAllReservation(String search, Integer limit, Integer skip);

    Optional<ReservationEntity> getReservationById(int id);

    ApiResponse<GenericResponse> deleteReservation(int id);

    ApiResponse<GenericResponse> updateReservation(int id, ReservationRequestDto request);


}
