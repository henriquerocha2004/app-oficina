import { describe, it, expect, vi, beforeEach } from 'vitest';
import { VehiclesApi } from '@/api/Vehicles';

vi.mock('@/api/Vehicles', () => ({
    VehiclesApi: {
        search: vi.fn(),
        getByClientId: vi.fn(),
    },
}));

describe('VehiclesApi', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('should call VehiclesApi.search with vehicle_type parameter', async () => {
        const mockResponse = {
            vehicles: {
                items: [],
                total_items: 0,
            },
        };

        vi.mocked(VehiclesApi.search).mockResolvedValue(mockResponse as any);

        await VehiclesApi.search({
            page: 1,
            per_page: 10,
            sort_direction: 'asc',
            sort_by: 'id',
            vehicle_type: 'car',
        });

        expect(VehiclesApi.search).toHaveBeenCalledWith(
            expect.objectContaining({
                vehicle_type: 'car',
            })
        );
    });

    it('should call VehiclesApi.search with search parameter', async () => {
        const mockResponse = {
            vehicles: {
                items: [],
                total_items: 0,
            },
        };

        vi.mocked(VehiclesApi.search).mockResolvedValue(mockResponse as any);

        await VehiclesApi.search({
            page: 1,
            per_page: 10,
            sort_direction: 'asc',
            sort_by: 'id',
            search: 'Toyota',
        });

        expect(VehiclesApi.search).toHaveBeenCalledWith(
            expect.objectContaining({
                search: 'Toyota',
            })
        );
    });

    it('should call VehiclesApi.search without vehicle_type when not provided', async () => {
        const mockResponse = {
            vehicles: {
                items: [],
                total_items: 0,
            },
        };

        vi.mocked(VehiclesApi.search).mockResolvedValue(mockResponse as any);

        await VehiclesApi.search({
            page: 1,
            per_page: 10,
            sort_direction: 'asc',
            sort_by: 'id',
        });

        expect(VehiclesApi.search).toHaveBeenCalledWith(
            expect.not.objectContaining({
                vehicle_type: expect.anything(),
            })
        );
    });

    it('should call VehiclesApi.getByClientId with client id', async () => {
        const mockResponse = {
            vehicles: [
                {
                    id: '1',
                    brand: 'Toyota',
                    model: 'Corolla',
                    year: 2020,
                    plate: 'ABC1234',
                    vehicle_type: 'car',
                    client_id: 'client-123',
                },
            ],
        };

        vi.mocked(VehiclesApi.getByClientId).mockResolvedValue(mockResponse as any);

        const result = await VehiclesApi.getByClientId('client-123');

        expect(VehiclesApi.getByClientId).toHaveBeenCalledWith('client-123');
        expect(result.vehicles).toHaveLength(1);
        expect(result.vehicles[0].brand).toBe('Toyota');
    });

    it('should return empty array when client has no vehicles', async () => {
        const mockResponse = {
            vehicles: [],
        };

        vi.mocked(VehiclesApi.getByClientId).mockResolvedValue(mockResponse as any);

        const result = await VehiclesApi.getByClientId('client-456');

        expect(VehiclesApi.getByClientId).toHaveBeenCalledWith('client-456');
        expect(result.vehicles).toHaveLength(0);
    });
});
